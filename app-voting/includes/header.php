<?php
// Cara pakai di halaman pemilih:
//   require_once __DIR__ . '/../config/app.php';
//   $judul = 'Daftar Kontestan';
//   require_once ROOT_PATH . '/includes/header.php';
//   ... isi halaman ...
//   require_once ROOT_PATH . '/includes/footer.php';
require_once __DIR__ . '/../config/app.php';

$bodyClass = 'body-situs';
require __DIR__ . '/head.php';
require __DIR__ . '/navbar.php';
?>
<main>
  <div class="container py-5">
    <?php require __DIR__ . '/flash.php'; ?>