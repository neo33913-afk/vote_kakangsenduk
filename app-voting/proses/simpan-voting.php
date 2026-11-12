<?php
require_once __DIR__ . '/../config/app.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('pages/voting.php');
}
csrf_verify();

if (!is_logged_in()) {
    set_flash('danger', 'Silakan login terlebih dahulu.');
    redirect('auth/login.php');
}
if (is_admin()) {
    set_flash('danger', 'Akun admin tidak dapat memilih.');
    redirect('pages/voting.php');
}

$userId       = (int) current_user()['id'];
$kontestanId  = filter_input(INPUT_POST, 'kontestan_id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

if (!periode_aktif($pdo)) {
    set_flash('danger', 'Voting sedang tidak dibuka.');
    redirect('pages/voting.php');
}

// Kategori diambil dari database, bukan dari kiriman form
$k = null;
if ($kontestanId) {
    $stmt = $pdo->prepare('SELECT id, kategori FROM kontestan WHERE id = ?');
    $stmt->execute([$kontestanId]);
    $k = $stmt->fetch() ?: null;
}
if (!$k) {
    set_flash('danger', 'Kontestan tidak ditemukan.');
    redirect('pages/voting.php');
}

$tujuan = 'pages/voting.php?kategori=' . urlencode($k['kategori']);

try {
    $ins = $pdo->prepare('INSERT INTO voting (user_id, kontestan_id, kategori) VALUES (?, ?, ?)');
    $ins->execute([$userId, $k['id'], $k['kategori']]);
} catch (PDOException $e) {
    // 1062 = pelanggaran UNIQUE (user_id, kategori): sudah memilih, atau klik ganda
    if ((int) ($e->errorInfo[1] ?? 0) === 1062) {
        set_flash('warning', 'Kamu sudah memberikan suara untuk kategori ' . $k['kategori'] . '.');
        redirect($tujuan);
    }
    error_log($e->getMessage());
    set_flash('danger', 'Terjadi kesalahan di server. Silakan coba lagi.');
    redirect($tujuan);
}

set_flash('success', 'Suaramu untuk kategori ' . $k['kategori'] . ' sudah tercatat. Terima kasih.');
redirect($tujuan);