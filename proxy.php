<?php
/**
 * proxy.php — CDN Node Proxy Streamer
 *
 * Taruh file ini di SEMUA server CDN eksternal (cdn2, cdn3, dst)
 * Server 1 (internal/cdn1) tidak butuh file ini
 *
 * Alur:
 *   stream.php (Server 1) → generate token → redirect ke proxy.php (CDN eksternal)
 *   proxy.php → validasi token → cURL stream video → browser
 *   Jika gagal → tandai node DOWN di cdn_status.json via stream.php callback
 */

declare(strict_types=1);

// ── Matikan buffering PHP (penting untuk Apache & OLS) ────────
@ini_set('output_buffering', 'off');
@ini_set('zlib.output_compression', false);
@ini_set('implicit_flush', true);
while (ob_get_level()) ob_end_clean();

// ── Load config ───────────────────────────────────────────────
$config = require __DIR__ . '/includes/cdn_nodes.php';

define('PROXY_SECRET', $config['secret']);
define('PROXY_TTL',    $config['token_ttl']);

// ── Ambil & validasi parameter ────────────────────────────────
$payload   = trim($_GET['d'] ?? '');
$signature = trim($_GET['s'] ?? '');

if ($payload === '' || $signature === '') {
    http_response_code(400);
    header('Content-Type: text/plain');
    exit('Bad Request');
}

// ── Validasi signature ────────────────────────────────────────
$expectedSig = hash_hmac('sha256', $payload, PROXY_SECRET);
if (!hash_equals($expectedSig, $signature)) {
    http_response_code(403);
    header('Content-Type: text/plain');
    exit('Forbidden');
}

// ── Decode payload ────────────────────────────────────────────
$data = json_decode(base64_decode($payload), true);

if (!is_array($data) || empty($data['url']) || empty($data['exp']) || empty($data['code'])) {
    http_response_code(400);
    header('Content-Type: text/plain');
    exit('Invalid payload');
}

// ── Validasi expiry ───────────────────────────────────────────
if (time() > (int) $data['exp']) {
    http_response_code(410);
    header('Content-Type: text/plain');
    exit('Token expired');
}

// ── Validasi URL ──────────────────────────────────────────────
$url     = $data['url'];
$code    = $data['code'];
$nodeKey = $data['node'] ?? 'unknown'; // cdn2 / cdn3 / dst

if (empty($url) || !filter_var($url, FILTER_VALIDATE_URL)) {
    http_response_code(500);
    header('Content-Type: text/plain');
    exit('Invalid URL');
}

// ── CORS — wajib agar browser tidak block saat crossorigin="anonymous" ──
// Hanya izinkan request dari domain player resmi
$allowedOrigins = [
    'https://play.vidshare.my.id',
    'https://upload.vidshare.my.id',
];
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, $allowedOrigins, true)) {
    header('Access-Control-Allow-Origin: ' . $origin);
} else {
    // Fallback: izinkan player utama selalu
    header('Access-Control-Allow-Origin: https://play.vidshare.my.id');
}
header('Access-Control-Allow-Methods: GET, HEAD, OPTIONS');
header('Access-Control-Allow-Headers: Range, Content-Type');
header('Access-Control-Expose-Headers: Content-Length, Content-Range, Accept-Ranges, Content-Type');
header('Access-Control-Max-Age: 86400');
header('Vary: Origin');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// ── Header statis ─────────────────────────────────────────────
header('Accept-Ranges: bytes');
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');
header('X-Accel-Buffering: no');
header('X-CDN-Node: ' . $nodeKey); // info node untuk debugging (hanya terlihat di DevTools)

// ── Siapkan request headers ke CDN external ───────────────────
$requestHeaders = [
    'User-Agent: ' . ($_SERVER['HTTP_USER_AGENT'] ?? 'Mozilla/5.0'),
    'Accept: video/mp4,video/*,*/*;q=0.8',
    'Referer: ' . (parse_url($url, PHP_URL_SCHEME) . '://' . parse_url($url, PHP_URL_HOST) . '/'),
];

if (isset($_SERVER['HTTP_RANGE'])) {
    $requestHeaders[] = 'Range: ' . $_SERVER['HTTP_RANGE'];
}

// ── Header yang di-forward dari CDN ke browser ────────────────
$forwardHeaders = [
    'content-type'   => true,
    'content-length' => true,
    'content-range'  => true,
    'accept-ranges'  => true,
];

// ── cURL stream ───────────────────────────────────────────────
$streamError = false;

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_HTTPHEADER     => $requestHeaders,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_MAXREDIRS      => 5,
    CURLOPT_TIMEOUT        => 0,
    CURLOPT_CONNECTTIMEOUT => 10,
    CURLOPT_SSL_VERIFYPEER => true,
    CURLOPT_SSL_VERIFYHOST => 2,
    CURLOPT_RETURNTRANSFER => false,
    CURLOPT_BUFFERSIZE     => 512 * 1024,

    CURLOPT_HEADERFUNCTION => function ($ch, $line) use ($forwardHeaders): int {
        $raw = $line;
        $l   = trim($line);

        if (empty($l)) return strlen($raw);

        if (preg_match('/^HTTP\/[\d.]+\s+(\d+)/i', $l, $m)) {
            http_response_code((int) $m[1]);
            return strlen($raw);
        }

        $colon = strpos($l, ':');
        if ($colon !== false) {
            $name = strtolower(trim(substr($l, 0, $colon)));
            if (isset($forwardHeaders[$name])) {
                header($l, true);
            }
        }

        return strlen($raw);
    },

    CURLOPT_WRITEFUNCTION  => function ($ch, $data): int {
        $len = strlen($data);
        echo $data;
        flush();
        return $len;
    },
]);

curl_exec($ch);

if (curl_errno($ch)) {
    $streamError = true;
    error_log(
        '[proxy.php] cURL error ' . curl_errno($ch) . ': ' . curl_error($ch) .
        ' | node: ' . $nodeKey .
        ' | code: ' . $code
    );
}

curl_close($ch);

// ── Tandai node DOWN jika stream gagal ────────────────────────
// Lapor ke Server 1 via callback agar cdn_status.json di-update
// (proxy.php tidak punya akses langsung ke cdn_status.json Server 1)
if ($streamError) {

    $callbackConfig  = $config['health'] ?? [];
    $callbackBaseUrl = $callbackConfig['callback_url'] ?? null;

    if ($callbackBaseUrl) {
        $cbPayload   = base64_encode(json_encode([
            'node' => $nodeKey,
            'exp'  => time() + 30,
        ]));
        $cbSignature = hash_hmac('sha256', $cbPayload, PROXY_SECRET);
        $cbUrl       = $callbackBaseUrl
            . '?d=' . urlencode($cbPayload)
            . '&s=' . urlencode($cbSignature);

        // Fire-and-forget — tidak tunggu response
        $cbCh = curl_init($cbUrl);
        curl_setopt_array($cbCh, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 3,
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
        ]);
        curl_exec($cbCh);
        curl_close($cbCh);
    }
}