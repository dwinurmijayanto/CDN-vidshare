<?php
/**
 * cdn_nodes.php — Konfigurasi CDN Node
 *
 * ⚠️  File ini SAMA di semua server CDN.
 *     Setelah deploy ke Render, update 'cdn4' dengan URL Render kamu.
 *     Lalu copy file ini juga ke Server 1 (Digital Ocean) agar stream.php tahu node baru.
 */

return [

    'nodes' => [
        'cdn1' => 'internal',                                    // Server 1 — DO (last resort)
        'cdn2' => 'https://cdn2.vidshare.my.id/proxy.php',      // Server 2 — OLS
        'cdn3' => 'https://cdn3.vidshare.my.id/proxy.php',      // Server 3 — OLS
         'cdn4' => 'https://cdn-vidshare.onrender.com/proxy.php',      // Server 3 — OLS
        // Setelah deploy ke Render, uncomment & isi URL di bawah:
        // 'cdn4' => 'https://NAMA-SERVICE.onrender.com/proxy.php',
    ],

    'method' => 'roundrobin',

    'token_ttl' => 30,

    // ⚠️  Harus sama persis di SEMUA server
    'secret' => 'xK9#mP2$qL7@nR4!',

    'health' => [
        'status_file'   => __DIR__ . '/../cdn_status.json',
        'stale_seconds' => 86400,
        'check_timeout' => 5,
        'ping_path'     => '/ping.php',
        'callback_url'  => 'https://play.vidshare.my.id/cdn_callback.php',
    ],

];
