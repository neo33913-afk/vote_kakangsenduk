<?php
// Escape output agar aman dari XSS
function e($str) {
    return htmlspecialchars((string) $str, ENT_QUOTES, 'UTF-8');
}

function redirect($path) {
    header('Location: ' . BASE_URL . '/' . ltrim($path, '/'));
    exit;
}

// Pesan satu kali tampil (flash)
function set_flash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}
function get_flash() {
    if (!isset($_SESSION['flash'])) return null;
    $f = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $f;
}

// Isian form dipertahankan saat terjadi error
function with_old(array $data) {
    $_SESSION['old'] = $data;
}
function pull_old() {
    $old = $_SESSION['old'] ?? [];
    unset($_SESSION['old']);
    return $old;
}

// CSRF
function csrf_token() {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}
function csrf_field() {
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}
function csrf_verify() {
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf'] ?? '', $token)) {
        http_response_code(419);
        exit('Sesi tidak valid. Silakan muat ulang halaman.');
    }
}

// Status login
function is_logged_in() {
    return isset($_SESSION['user']['id']);
}
function is_admin() {
    return is_logged_in() && $_SESSION['user']['role'] === 'admin';
}
function current_user() {
    return $_SESSION['user'] ?? null;
}

// Periode voting yang sedang aktif (status Aktif DAN dalam rentang waktu)
function periode_aktif(PDO $pdo) {
    $stmt = $pdo->query(
        "SELECT * FROM periode_voting
         WHERE status = 'Aktif' AND NOW() BETWEEN tanggal_mulai AND tanggal_selesai
         ORDER BY id DESC LIMIT 1"
    );
    return $stmt->fetch() ?: null;
}