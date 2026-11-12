<?php
// Cara pakai di halaman admin:
//   require_once __DIR__ . '/../config/app.php';
//   $judul = 'Dashboard';
//   require_once ROOT_PATH . '/includes/admin-header.php';
//   ... isi halaman ...
//   require_once ROOT_PATH . '/includes/admin-footer.php';
// Halaman tambah/edit kontestan: isi $menuAktif = 'kontestan.php' agar menu Kontestan tetap menyala.
require_once __DIR__ . '/admin-check.php';   // otomatis: wajib login dan role admin

$judul     = $judul ?? 'Admin';
$bodyClass = 'body-admin';
$menuAktif = $menuAktif ?? basename($_SERVER['SCRIPT_NAME']);
$admin     = current_user();

$menuAdmin = [
    'dashboard.php'    => ['Dashboard',    'bi-speedometer2'],
    'kontestan.php'    => ['Kontestan',    'bi-person-badge'],
    'pemilih.php'      => ['Pemilih',      'bi-people'],
    'hasil-voting.php' => ['Hasil Voting', 'bi-bar-chart'],
    'periode.php'      => ['Periode',      'bi-calendar-event'],
];

require __DIR__ . '/head.php';
?>
<div class="admin-latar" id="latarAdmin"></div>

<aside class="admin-sidebar" id="sidebarAdmin" aria-label="Menu admin">
  <div class="admin-merek">Kakang Senduk <small>Admin</small></div>

  <ul class="admin-menu">
    <?php foreach ($menuAdmin as $file => [$label, $ikon]): ?>
      <li>
        <a href="<?= BASE_URL ?>/admin/<?= $file ?>" class="<?= $menuAktif === $file ? 'aktif' : '' ?>">
          <i class="bi <?= $ikon ?>"></i><?= e($label) ?>
        </a>
      </li>
    <?php endforeach; ?>
  </ul>

  <div class="admin-bawah">
    <div class="mb-2"><?= e($admin['nama']) ?></div>
    <a href="<?= BASE_URL ?>/pages/index.php"><i class="bi bi-box-arrow-up-right"></i> Lihat situs</a>
    <a href="<?= BASE_URL ?>/auth/logout.php"><i class="bi bi-box-arrow-right"></i> Keluar</a>
  </div>
</aside>

<div class="admin-isi">
  <div class="admin-topbar">
    <button type="button" class="btn btn-outline-secondary btn-sm" data-menu-admin
            aria-controls="sidebarAdmin" aria-expanded="false" aria-label="Buka menu">
      <i class="bi bi-list"></i>
    </button>
    <span class="admin-topbar-judul">Admin</span>
  </div>

  <main class="admin-main">
    <?php require __DIR__ . '/flash.php'; ?>