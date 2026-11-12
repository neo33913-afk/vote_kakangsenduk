<?php
require_once __DIR__ . '/../includes/admin-check.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('admin/pemilih.php');
}
csrf_verify();

$id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
if (!$id) {
    set_flash('danger', 'Data tidak valid.');
    redirect('admin/pemilih.php');
}

$stmt = $pdo->prepare('SELECT id, nama, role FROM users WHERE id = ?');
$stmt->execute([$id]);
$u = $stmt->fetch();

if (!$u) {
    set_flash('danger', 'Akun tidak ditemukan.');
    redirect('admin/pemilih.php');
}
// Hanya akun pemilih yang bisa dihapus (admin, termasuk diri sendiri, tidak)
if ($u['role'] !== 'pemilih') {
    set_flash('danger', 'Hanya akun pemilih yang dapat dihapus.');
    redirect('admin/pemilih.php');
}

$cek = $pdo->prepare('SELECT COUNT(*) FROM voting WHERE user_id = ?');
$cek->execute([$id]);
if ((int) $cek->fetchColumn() > 0) {
    set_flash('danger', "Akun {$u['nama']} tidak dapat dihapus karena sudah memberikan suara.");
    redirect('admin/pemilih.php');
}

try {
    $pdo->prepare('DELETE FROM users WHERE id = ?')->execute([$id]);
    set_flash('success', "Akun {$u['nama']} berhasil dihapus.");
} catch (PDOException $e) {
    if ($e->getCode() === '23000') {
        set_flash('danger', "Akun {$u['nama']} tidak dapat dihapus karena sudah memberikan suara.");
    } else {
        error_log($e->getMessage());
        set_flash('danger', 'Terjadi kesalahan. Akun tidak terhapus.');
    }
}

redirect('admin/pemilih.php');