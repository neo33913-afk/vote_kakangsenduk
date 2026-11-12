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

$stmt = $pdo->prepare('SELECT * FROM kontestan WHERE id = ?');
$stmt->execute([$id]);
$lama = $stmt->fetch();
if (!$lama) {
    set_flash('danger', 'Kontestan tidak ditemukan.');
    redirect('admin/kontestan.php');
}

$kembali = 'admin/edit-kontestan.php?id=' . $id;
[$data, $errors] = ambil_input_kontestan($_POST);

if (!$errors) {
    if (nomor_terpakai($pdo, $data['kategori'], $data['nomor_peserta'], $id)) {
        $errors[] = "Nomor peserta {$data['nomor_peserta']} sudah dipakai di kategori {$data['kategori']}.";
    }
    if ($data['kategori'] !== $lama['kategori'] && jumlah_suara_kontestan($pdo, $id) > 0) {
        $errors[] = 'Kategori tidak dapat diubah karena kontestan ini sudah memiliki suara.';
    }
}

$fotoBaru = null;
if (!$errors) {
    try {
        $fotoBaru = proses_upload_foto($_FILES['foto'] ?? []);
    } catch (RuntimeException $e) {
        $errors[] = $e->getMessage();
    }
}

if ($errors) {
    with_old($data);
    set_flash('danger', implode(' ', $errors));
    redirect($kembali);
}

try {
    $stmt = $pdo->prepare(
        "UPDATE kontestan SET
            nomor_peserta = :nomor_peserta, nama = :nama, kategori = :kategori, foto = :foto,
            asal_instansi = :asal_instansi, tempat_lahir = :tempat_lahir, tanggal_lahir = :tanggal_lahir,
            visi = :visi, misi = :misi, prestasi = :prestasi, deskripsi = :deskripsi
         WHERE id = :id"
    );
    $params = array_map('kosong_jadi_null', $data);
    $params['foto'] = $fotoBaru ?? $lama['foto'];
    $params['id']   = $id;
    $stmt->execute($params);
} catch (PDOException $e) {
    hapus_foto($fotoBaru);
    with_old($data);
    if ($e->getCode() === '23000') {
        set_flash('danger', 'Nomor peserta sudah dipakai di kategori tersebut.');
        redirect($kembali);
    }
    error_log($e->getMessage());
    set_flash('danger', 'Terjadi kesalahan. Perubahan tidak tersimpan.');
    redirect($kembali);
}

// Foto lama dihapus hanya setelah UPDATE berhasil
if ($fotoBaru !== null) {
    hapus_foto($lama['foto']);
}

set_flash('success', "Data {$data['nama']} berhasil diperbarui.");
redirect('admin/kontestan.php');