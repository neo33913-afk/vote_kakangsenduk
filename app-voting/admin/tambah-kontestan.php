<?php
require_once __DIR__ . '/../config/app.php';

$old       = pull_old();
$judul     = 'Tambah Kontestan';
$menuAktif = 'kontestan.php';
require_once ROOT_PATH . '/includes/admin-header.php';

$v = fn($k) => e($old[$k] ?? '');
?>

<div class="admin-kepala">
  <h1 class="judul-halaman">Tambah Kontestan</h1>
  <a class="btn btn-outline-secondary btn-sm" href="<?= BASE_URL ?>/admin/kontestan.php">&larr; Kembali</a>
</div>

<form class="panel" method="post" enctype="multipart/form-data"
      action="<?= BASE_URL ?>/proses/kontestan-tambah.php" data-sekali="Menyimpan...">
  <?= csrf_field() ?>

  <div class="row g-3">
    <div class="col-md-4">
      <label class="form-label" for="kategori">Kategori</label>
      <select class="form-select" id="kategori" name="kategori" required>
        <?php foreach (['Kakang', 'Senduk'] as $kat): ?>
          <option value="<?= $kat ?>"<?= ($old['kategori'] ?? '') === $kat ? ' selected' : '' ?>><?= $kat ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="col-md-4">
      <label class="form-label" for="nomor_peserta">Nomor peserta</label>
      <input class="form-control" id="nomor_peserta" name="nomor_peserta" maxlength="10"
             value="<?= $v('nomor_peserta') ?>" placeholder="01" required>
      <div class="form-text">Unik per kategori.</div>
    </div>

    <div class="col-md-4">
      <label class="form-label" for="foto">Foto (JPG, PNG, WebP, maks 2 MB)</label>
      <input class="form-control" type="file" id="foto" name="foto" accept="image/jpeg,image/png,image/webp">
      <div class="form-text">Disarankan rasio potret 3:4.</div>
    </div>

    <div class="col-md-6">
      <label class="form-label" for="nama">Nama</label>
      <input class="form-control" id="nama" name="nama" maxlength="100" value="<?= $v('nama') ?>" required>
    </div>

    <div class="col-md-6">
      <label class="form-label" for="asal_instansi">Asal sekolah / instansi</label>
      <input class="form-control" id="asal_instansi" name="asal_instansi" maxlength="150"
             value="<?= $v('asal_instansi') ?>" required>
    </div>

    <div class="col-md-6">
      <label class="form-label" for="tempat_lahir">Tempat lahir</label>
      <input class="form-control" id="tempat_lahir" name="tempat_lahir" maxlength="100" value="<?= $v('tempat_lahir') ?>">
    </div>

    <div class="col-md-6">
      <label class="form-label" for="tanggal_lahir">Tanggal lahir</label>
      <input class="form-control" type="date" id="tanggal_lahir" name="tanggal_lahir"
             max="<?= date('Y-m-d') ?>" value="<?= $v('tanggal_lahir') ?>">
    </div>

    <div class="col-md-6">
      <label class="form-label" for="visi">Visi</label>
      <textarea class="form-control" id="visi" name="visi" rows="4" maxlength="5000"><?= $v('visi') ?></textarea>
    </div>

    <div class="col-md-6">
      <label class="form-label" for="misi">Misi</label>
      <textarea class="form-control" id="misi" name="misi" rows="4" maxlength="5000"><?= $v('misi') ?></textarea>
    </div>

    <div class="col-md-6">
      <label class="form-label" for="prestasi">Prestasi</label>
      <textarea class="form-control" id="prestasi" name="prestasi" rows="4" maxlength="5000"><?= $v('prestasi') ?></textarea>
    </div>

    <div class="col-md-6">
      <label class="form-label" for="deskripsi">Deskripsi</label>
      <textarea class="form-control" id="deskripsi" name="deskripsi" rows="4" maxlength="5000"><?= $v('deskripsi') ?></textarea>
    </div>
  </div>

  <div class="d-flex gap-2 mt-4">
    <button class="btn btn-primary" type="submit">Simpan</button>
    <a class="btn btn-outline-secondary" href="<?= BASE_URL ?>/admin/kontestan.php">Batal</a>
  </div>
</form>

<?php require_once ROOT_PATH . '/includes/admin-footer.php'; ?>