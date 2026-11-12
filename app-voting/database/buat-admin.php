<?php
// Pemakaian: php database/buat-admin.php "Nama" email@contoh.com password
if (php_sapi_name() !== 'cli') {
    exit('Hanya bisa dijalankan lewat terminal.');
}
require_once __DIR__ . '/../config/koneksi.php';

[$script, $nama, $email, $password] = $argv + [null, null, null, null];
if (!$nama || !$email || !$password) {
    exit("Pemakaian: php database/buat-admin.php \"Nama\" email@contoh.com password\n");
}
if (strlen($password) < 8) {
    exit("Password minimal 8 karakter.\n");
}

$stmt = $pdo->prepare(
    "INSERT INTO users (nama, email, no_hp, password, role) VALUES (?, ?, '-', ?, 'admin')"
);
$stmt->execute([$nama, strtolower($email), password_hash($password, PASSWORD_DEFAULT)]);

echo "Admin berhasil dibuat.\n";