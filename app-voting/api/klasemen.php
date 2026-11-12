<?php
require_once __DIR__ . '/../config/app.php';
require_once ROOT_PATH . '/includes/klasemen.php';

// API ini hanya membaca; lepas kunci session agar polling berkala tidak menahan request lain
session_write_close();

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Method tidak diizinkan.']);
    exit;
}

$kategori = $_GET['kategori'] ?? 'Kakang';
if (!in_array($kategori, KATEGORI_VALID, true)) {
    http_response_code(400);
    echo json_encode(['error' => 'Kategori tidak valid. Gunakan Kakang atau Senduk.']);
    exit;
}

try {
    echo json_encode(ambil_klasemen($pdo, $kategori), JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    error_log($e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Gagal mengambil klasemen.']);
}