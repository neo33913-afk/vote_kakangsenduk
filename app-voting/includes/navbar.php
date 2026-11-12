<?php
$user    = current_user();
$halaman = basename($_SERVER['SCRIPT_NAME']);
$menu = [
    'index.php'     => ['Beranda',   'pages/index.php'],
    'kontestan.php' => ['Kontestan', 'pages/kontestan.php'],
    'voting.php'    => ['Voting',    'pages/voting.php'],
    'klasemen.php'  => ['Klasemen',  'pages/klasemen.php'],
];
?>
<header class="situs-nav">
  <nav class="navbar navbar-expand-md" aria-label="Navigasi utama">
    <div class="container">
      <a class="merek" href="<?= BASE_URL ?>/pages/index.php">
        <span class="merek-nama">Kakang Senduk</span>
        <span class="merek-sub">Ponorogo</span>
      </a>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
              data-bs-target="#menuUtama" aria-controls="menuUtama"
              aria-expanded="false" aria-label="Buka menu">
        <i class="bi bi-list"></i>
      </button>

      <div class="collapse navbar-collapse" id="menuUtama">
        <ul class="navbar-nav ms-md-4 me-auto">
          <?php foreach ($menu as $file => [$label, $url]): ?>
            <li class="nav-item">
              <a class="nav-link<?= $halaman === $file ? ' active' : '' ?>"
                 href="<?= BASE_URL . '/' . $url ?>"><?= e($label) ?></a>
            </li>
          <?php endforeach; ?>
        </ul>

        <div class="menu-akun d-flex align-items-center gap-2">
          <?php if ($user): ?>
            <span class="teks-kecil teks-2 me-1"><?= e($user['nama']) ?></span>
            <?php if (is_admin()): ?>
              <a class="btn btn-outline-secondary btn-sm" href="<?= BASE_URL ?>/admin/dashboard.php">Panel Admin</a>
            <?php endif; ?>
            <a class="btn btn-outline-secondary btn-sm" href="<?= BASE_URL ?>/auth/logout.php">Keluar</a>
          <?php else: ?>
            <a class="btn btn-outline-secondary btn-sm" href="<?= BASE_URL ?>/auth/login.php">Masuk</a>
            <a class="btn btn-primary btn-sm" href="<?= BASE_URL ?>/auth/register.php">Daftar</a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </nav>
</header>