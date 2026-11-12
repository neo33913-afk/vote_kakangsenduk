<?php
require_once __DIR__ . '/../config/app.php';

if (is_logged_in()) {
    redirect(is_admin() ? 'admin/dashboard.php' : 'pages/index.php');
}

$old   = pull_old();
$judul = 'Daftar';
require_once ROOT_PATH . '/includes/header.php';
?>

<div class="row justify-content-center">
  <div class="col-md-8 col-lg-5">
    <h1 class="judul-halaman mb-1">Daftar</h1>
    <p class="teks-2 mb-4">Buat akun untuk ikut memilih Kakang dan Senduk.</p>

    <form class="panel" method="post" action="<?= BASE_URL ?>/proses/register-proses.php" data-sekali="Mendaftarkan...">
      <?= csrf_field() ?>

      <div class="mb-3">
        <label class="form-label" for="nama">Nama lengkap</label>
        <input class="form-control" type="text" id="nama" name="nama"
               value="<?= e($old['nama'] ?? '') ?>" maxlength="100"
               autocomplete="name" required autofocus>
      </div>

      <div class="mb-3">
        <label class="form-label" for="email">Email</label>
        <input class="form-control" type="email" id="email" name="email"
               value="<?= e($old['email'] ?? '') ?>" maxlength="100"
               autocomplete="email" required>
      </div>

      <div class="mb-3">
        <label class="form-label" for="no_hp">Nomor HP</label>
        <input class="form-control" type="tel" id="no_hp" name="no_hp"
               value="<?= e($old['no_hp'] ?? '') ?>" maxlength="20"
               inputmode="numeric" autocomplete="tel" placeholder="081234567890" required>
      </div>

      <div class="mb-4">
        <label class="form-label" for="password">Password</label>
        <input class="form-control" type="password" id="password" name="password"
               minlength="8" autocomplete="new-password" required>
        <div class="form-text">Minimal 8 karakter.</div>
      </div>

      <button class="btn btn-primary w-100" type="submit">Daftar</button>
    </form>

    <p class="teks-kecil teks-2 mt-3 mb-0">
      Sudah punya akun? <a href="<?= BASE_URL ?>/auth/login.php">Masuk</a>
    </p>
  </div>
</div>

<?php require_once ROOT_PATH . '/includes/footer.php'; ?>