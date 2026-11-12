<?php
require_once __DIR__ . '/../config/app.php';
require_once ROOT_PATH . '/includes/admin-check.php';   // wajib login dan admin
require_once ROOT_PATH . '/includes/kontestan.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('admin/tambah-kontestan.php');
}
csrf_verify();

[$data, $errors] = ambil_input_kontestan($_POST);

if (!$errors && nomor_terpakai($pdo, $data['kategori'], $data['nomor_peserta'])) {
    $errors[] = 'Nomor peserta ' . $data['nomor_peserta'] . ' sudah dipakai di kategori ' . $data['kategori'] . '.';
}

// Foto diproses terakhir, hanya kalau data lain sudah valid
$foto = null;
if (!$errors) {
    try {
        $foto = proses_upload_foto($_FILES['foto'] ?? []);
    } catch (RuntimeException $ex) {
        $errors[] = $ex->getMessage();
    }
}

if ($errors) {
    with_old($data);
    set_flash('danger', implode(' ', $errors));
    redirect('admin/tambah-kontestan.php');
}

try {
    $stmt = $pdo->prepare(
        'INSERT INTO kontestan
           (nomor_peserta, nama, kategori, foto, asal_instansi, tempat_lahir, tanggal_lahir, visi, misi, prestasi, deskripsi)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([
        $data['nomor_peserta'],
        $data['nama'],
        $data['kategori'],
        $foto,
        $data['asal_instansi'],
        kosong_jadi_null($data['tempat_lahir']),
        kosong_jadi_null($data['tanggal_lahir']),
        kosong_jadi_null($data['visi']),
        kosong_jadi_null($data['misi']),
        kosong_jadi_null($data['prestasi']),
        kosong_jadi_null($data['deskripsi']),
    ]);
} catch (PDOException $ex) {
    hapus_foto($foto);   // jangan tinggalkan file yatim
    with_old($data);
    if ((int) ($ex->errorInfo[1] ?? 0) === 1062) {
        set_flash('danger', 'Nomor peserta sudah dipakai di kategori ini.');
    } else {
        error_log($ex->getMessage());
        set_flash('danger', 'Terjadi kesalahan di server. Silakan coba lagi.');
    }
    redirect('admin/tambah-kontestan.php');
}

set_flash('success', 'Kontestan ' . $data['nama'] . ' berhasil ditambahkan.');
redirect('admin/kontestan.php');