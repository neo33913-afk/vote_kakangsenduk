<?php
require_once __DIR__ . '/../includes/admin-check.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('admin/periode.php');
}
csrf_verify();

// Input <input type="datetime-local"> dikirim sebagai "2026-11-20T08:00"
function parse_waktu(string $s): ?DateTime
{
    foreach (['Y-m-d\TH:i', 'Y-m-d\TH:i:s', 'Y-m-d H:i:s'] as $fmt) {
        $d = DateTime::createFromFormat($fmt, $s);
        if ($d && $d->format($fmt) === $s) {
            return $d;
        }
    }
    return null;
}

$idRaw = trim($_POST['id'] ?? '');
$id    = $idRaw === '' ? null : filter_var($idRaw, FILTER_VALIDATE_INT);
if ($idRaw !== '' && !$id) {
    set_flash('danger', 'Data tidak valid.');
    redirect('admin/periode.php');
}

$nama       = trim($_POST['nama_periode'] ?? '');
$mulaiRaw   = trim($_POST['tanggal_mulai'] ?? '');
$selesaiRaw = trim($_POST['tanggal_selesai'] ?? '');
$mulai      = parse_waktu($mulaiRaw);
$selesai    = parse_waktu($selesaiRaw);

$errors = [];
if ($nama === '' || mb_strlen($nama) > 100) {
    $errors[] = 'Nama periode wajib diisi (maksimal 100 karakter).';
}
if (!$mulai)   $errors[] = 'Tanggal mulai tidak valid.';
if (!$selesai) $errors[] = 'Tanggal selesai tidak valid.';
if ($mulai && $selesai && $selesai <= $mulai) {
    $errors[] = 'Tanggal selesai harus setelah tanggal mulai.';
}

if ($id && !$errors) {
    $cek = $pdo->prepare('SELECT id FROM periode_voting WHERE id = ?');
    $cek->execute([$id]);
    if (!$cek->fetch()) {
        $errors[] = 'Periode tidak ditemukan.';
    }
}

if ($errors) {
    with_old([
        'id' => $idRaw, 'nama_periode' => $nama,
        'tanggal_mulai' => $mulaiRaw, 'tanggal_selesai' => $selesaiRaw,
    ]);
    set_flash('danger', implode(' ', $errors));
    redirect('admin/periode.php');
}

$mulaiSql   = $mulai->format('Y-m-d H:i:s');
$selesaiSql = $selesai->format('Y-m-d H:i:s');

try {
    if ($id) {
        $pdo->prepare(
            'UPDATE periode_voting SET nama_periode = ?, tanggal_mulai = ?, tanggal_selesai = ? WHERE id = ?'
        )->execute([$nama, $mulaiSql, $selesaiSql, $id]);
        set_flash('success', 'Periode berhasil diperbarui.');
    } else {
        $pdo->prepare(
            "INSERT INTO periode_voting (nama_periode, tanggal_mulai, tanggal_selesai, status)
             VALUES (?, ?, ?, 'Nonaktif')"
        )->execute([$nama, $mulaiSql, $selesaiSql]);
        set_flash('success', 'Periode berhasil ditambahkan. Aktifkan periode agar voting dibuka.');
    }
} catch (PDOException $e) {
    error_log($e->getMessage());
    set_flash('danger', 'Terjadi kesalahan. Data tidak tersimpan.');
}

redirect('admin/periode.php');