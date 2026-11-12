<?php
// Bagian <head> dipakai bersama oleh halaman pemilih dan admin.
// Sebelum di-include, halaman boleh mengisi: $judul dan $bodyClass
require_once __DIR__ . '/../config/app.php';

$judul     = $judul ?? 'Beranda';
$bodyClass = $bodyClass ?? '';
$versiCss  = filemtime(ROOT_PATH . '/assets/css/style.css');   // agar perubahan CSS langsung termuat
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($judul) ?> | Pemilihan Kakang Senduk Ponorogo</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,700&family=Plus+Jakarta+Sans:wght@400;500;700&display=swap">

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <!-- CSS kita dimuat TERAKHIR supaya menimpa Bootstrap -->
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css?v=<?= $versiCss ?>">
</head>
<body class="<?= e($bodyClass) ?>">