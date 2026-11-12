<?php
require_once __DIR__ . '/auth-check.php';

if (!is_admin()) {
    http_response_code(403);
    exit('Akses ditolak. Halaman ini khusus administrator.');
}