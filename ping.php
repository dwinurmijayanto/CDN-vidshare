<?php
/**
 * ping.php — Endpoint Health Check CDN Node
 *
 * Taruh file ini di ROOT setiap server CDN (cdn2, cdn3)
 * Diakses oleh health_check.php saat melakukan pengecekan
 *
 * Response:
 *   200 OK  → node UP
 *   selain itu → node dianggap DOWN
 */

http_response_code(200);
header('Content-Type: text/plain');
header('Cache-Control: no-store');
echo 'OK';