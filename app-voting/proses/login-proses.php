<?php
require_once __DIR__ . '/../config/app.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('auth/login.php');
}
csrf_verify();

$email    = strtolower(trim($_POST['email'] ?? ''));
$password = $_POST['password'] ?? '';

$stmt = $pdo->prepare('SELECT id, nama, email, password, role FROM users WHERE email = ?');
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password'])) {
    with_old(['email' => $email]);
    // Pesan sengaja sama, agar tidak membocorkan email mana yang terdaftar
    set_flash('danger', 'Email atau password salah.');
    redirect('auth/login.php');
}

session_regenerate_id(true);   // cegah session fixation
$_SESSION['user'] = [
    'id'    => (int) $user['id'],
    'nama'  => $user['nama'],
    'email' => $user['email'],
    'role'  => $user['role'],
];

redirect($user['role'] === 'admin' ? 'admin/dashboard.php' : 'pages/index.php');