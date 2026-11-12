<?php
require_once __DIR__ . '/../includes/admin-check.php';
require_once ROOT_PATH . '/includes/kontestan.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('admin/kontestan.php');
}
csrf_verify();

$id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
if (!$id) {
    set_flash('danger', 'Data tidak valid.');
    redirect('admin/kontestan.php');
}

$stmt = $pdo->prepare('SELECT id, nama, foto FROM kontestan WHERE id = ?');
$stmt->execute([$id]);
$k = $stmt->fetch();
if (!$k) {
    set_flash('danger', 'Kontestan tidak ditemukan.');
    redirect('admin/kontestan.php');
}

if (jumlah_suara_kontestan($pdo, $id) > 0) {
    set_flash('danger', "{$k['nama']} tidak dapat dihapus karena sudah memiliki suara.");
    redirect('admin/kontestan.php');
}

try {
    $pdo->prepare('DELETE FROM kontestan WHERE id = ?')->execute([$id]);
} catch (PDOException $e) {
    // 23000 = foreign key (misalnya ada suara masuk tepat saat dihapus)
    if ($e->getCode() === '23000') {
        set_flash('danger', "{$k['nama']} tidak dapat dihapus karena sudah memiliki suara.");
    } else {
        error_log($e->getMessage());
        set_flash('danger', 'Terjadi kesalahan. Data tidak terhapus.');
    }
    redirect('admin/kontestan.php');
}

hapus_foto($k['foto']);

set_flash('success', "Kontestan {$k['nama']} berhasil dihapus.");
redirect('admin/kontestan.php');