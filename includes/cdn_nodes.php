<?php
/**
 * cdn_nodes.php — Konfigurasi CDN Node untuk Load Balancing Stream
 *
 * Cara pakai:
 *   - Tambah server  → tambah baris baru di 'nodes'
 *   - Hapus server   → comment atau hapus barisnya
 *   - Ganti domain   → edit value-nya saja
 *   - stream.php, proxy.php, health_check.php tidak perlu disentuh
 *
 * Catatan:
 *   - 'internal' = Server 1 (CDN1) → TIDAK di-health check, selalu jadi last resort
 *   - URL lainnya = node eksternal  → di-health check tiap hari
 */

return [

    // ── Daftar CDN Node ───────────────────────────────────────────
    'nodes' => [
        'cdn1' => 'internal',                                    // Server 1 — OLS 157.245.130.247 (last resort)
        'cdn2' => 'https://cdn2.vidshare.my.id/proxy.php',      // Server 2 — OLS 147.182.218.10
        'cdn3' => 'https://cdn3.vidshare.my.id/proxy.php',      // Server 3 — OLS 8.219.241.150
    ],

    // ── Metode Load Balancing ─────────────────────────────────────
    // 'random'     : acak setiap request — merata jangka panjang
    // 'roundrobin' : bergantian per detik — paling adil
    // 'codebased'  : hash code → node tetap (1 video selalu ke node sama)
    'method' => 'roundrobin',

    // ── Token TTL ─────────────────────────────────────────────────
    // Berapa detik token valid setelah di-generate
    'token_ttl' => 30,

    // ── Secret Key ───────────────────────────────────────────────
    // Harus sama persis di SEMUA server
    'secret' => 'xK9#mP2$qL7@nR4!',

    // ── Health Check ─────────────────────────────────────────────
    'health' => [
        // Path file JSON cache status node (relative dari root project)
        'status_file' => __DIR__ . '/../cdn_status.json',

        // Stale duration — 86400 = 1 hari
        // Setelah waktu ini, health check harian akan reset & re-check semua node
        'stale_seconds' => 86400,

        // Timeout koneksi saat health check (detik)
        'check_timeout' => 5,

        // Endpoint yang di-ping saat health check
        // File ping.php harus ada di root setiap CDN server
        'ping_path' => '/ping.php',

        // Callback URL di Server 1 untuk menerima laporan failure dari proxy node
        // Diisi URL lengkap ke cdn_callback.php di Server 1
        'callback_url' => 'https://play.vidshare.my.id/cdn_callback.php',
    ],

];
