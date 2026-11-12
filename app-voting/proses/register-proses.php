<?php
require_once __DIR__ . '/../config/app.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('auth/register.php');
}
csrf_verify();

$nama     = trim($_POST['nama'] ?? '');
$email    = strtolower(trim($_POST['email'] ?? ''));
$no_hp    = trim($_POST['no_hp'] ?? '');
$password = $_POST['password'] ?? '';

$errors = [];
if ($nama === '' || mb_strlen($nama) > 100) {
    $errors[] = 'Nama wajib diisi (maksimal 100 karakter).';
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 100) {
    $errors[] = 'Format email tidak valid.';
}
if (!preg_match('/^(\+62|62|0)[0-9]{8,13}$/', $no_hp)) {
    $errors[] = 'Nomor HP tidak valid.';
}
if (strlen($password) < 8) {
    $errors[] = 'Password minimal 8 karakter.';
}

if (!$errors) {
    $cek = $pdo->prepare('SELECT id FROM users WHERE email = ?');
    $cek->execute([$email]);
    if ($cek->fetch()) {
        $errors[] = 'Email sudah terdaftar.';
    }
}

if ($errors) {
    with_old(['nama' => $nama, 'email' => $email, 'no_hp' => $no_hp]);
    set_flash('danger', implode(' ', $errors));
    redirect('auth/register.php');
}

try {
    $stmt = $pdo->prepare(
        "INSERT INTO users (nama, email, no_hp, password, role) VALUES (?, ?, ?, ?, 'pemilih')"
    );
    $stmt->execute([$nama, $email, $no_hp, password_hash($password, PASSWORD_DEFAULT)]);
} catch (PDOException $e) {
    // 23000 = pelanggaran UNIQUE (email didaftarkan bersamaan)
    if ($e->getCode() === '23000') {
        with_old(['nama' => $nama, 'email' => $email, 'no_hp' => $no_hp]);
        set_flash('danger', 'Email sudah terdaftar.');
        redirect('auth/register.php');
    }
    throw $e;
}

set_flash('success', 'Registrasi berhasil. Silakan login.');
redirect('auth/login.php');