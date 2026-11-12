<?php
require_once __DIR__ . '/../config/app.php';

if (is_logged_in()) {
    redirect(is_admin() ? 'admin/dashboard.php' : 'pages/index.php');
}

$old   = pull_old();
$judul = 'Masuk';
require_once ROOT_PATH . '/includes/header.php';
?>

<div class="row justify-content-center">
  <div class="col-md-8 col-lg-5">
    <h1 class="judul-halaman mb-1">Masuk</h1>
    <p class="teks-2 mb-4">Masuk untuk memberikan suara.</p>

    <form class="panel" method="post" action="<?= BASE_URL ?>/proses/login-proses.php" data-sekali="Memeriksa...">
      <?= csrf_field() ?>

      <div class="mb-3">
        <label class="form-label" for="email">Email</label>
        <input class="form-control" type="email" id="email" name="email"
               value="<?= e($old['email'] ?? '') ?>" maxlength="100"
               autocomplete="email" required autofocus>
      </div>

      <div class="mb-4">
        <label class="form-label" for="password">Password</label>
        <input class="form-control" type="password" id="password" name="password"
               autocomplete="current-password" required>
      </div>

      <button class="btn btn-primary w-100" type="submit">Masuk</button>
    </form>

    <p class="teks-kecil teks-2 mt-3 mb-0">
      Belum punya akun? <a href="<?= BASE_URL ?>/auth/register.php">Daftar</a>
    </p>
  </div>
</div>

<?php require_once ROOT_PATH . '/includes/footer.php'; ?>