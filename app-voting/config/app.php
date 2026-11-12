<?php
date_default_timezone_set('Asia/Jakarta');

// Sesuaikan jika Apache kamu bukan di port 80 (misal localhost:8080)
define('BASE_URL', 'http://localhost/app-voting');
define('ROOT_PATH', dirname(__DIR__));
define('UPLOAD_PATH', ROOT_PATH . '/uploads');
define('UPLOAD_URL', BASE_URL . '/uploads');

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
    session_start();
}

require_once __DIR__ . '/koneksi.php';
require_once ROOT_PATH . '/includes/functions.php';