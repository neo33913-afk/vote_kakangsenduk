<?php
require_once __DIR__ . '/../includes/admin-check.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('admin/periode.php');
}
csrf_verify();

$id   = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
$aksi = $_POST['aksi'] ?? '';

if (!$id || !in_array($aksi, ['aktifkan', 'tutup'], true)) {
    set_flash('danger', 'Data tidak valid.');
    redirect('admin/periode.php');
}

$cek = $pdo->prepare('SELECT id FROM periode_voting WHERE id = ?');
$cek->execute([$id]);
if (!$cek->fetch()) {
    set_flash('danger', 'Periode tidak ditemukan.');
    redirect('admin/periode.php');
}

try {
    $pdo->beginTransaction();

    if ($aksi === 'aktifkan') {
        // Hanya satu periode yang boleh aktif
        $pdo->prepare("UPDATE periode_voting SET status = 'Nonaktif' WHERE id <> ?")->execute([$id]);
        $pdo->prepare("UPDATE periode_voting SET status = 'Aktif' WHERE id = ?")->execute([$id]);
    } else {
        $pdo->prepare("UPDATE periode_voting SET status = 'Nonaktif' WHERE id = ?")->execute([$id]);
    }

    $pdo->commit();
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log($e->getMessage());
    set_flash('danger', 'Terjadi kesalahan. Status tidak berubah.');
    redirect('admin/periode.php');
}

if ($aksi === 'tutup') {
    set_flash('success', 'Voting ditutup.');
} else {
    // Voting benar-benar terbuka hanya jika waktu sekarang ada di dalam rentang periode
    $buka = periode_aktif($pdo);
    if ($buka && (int) $buka['id'] === $id) {
        set_flash('success', 'Periode diaktifkan. Voting sekarang dibuka.');
    } else {
        set_flash('warning', 'Periode diaktifkan, tetapi waktu sekarang di luar rentang tanggalnya, jadi voting belum terbuka.');
    }
}

redirect('admin/periode.php');