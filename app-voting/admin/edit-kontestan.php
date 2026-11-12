<?php
require_once __DIR__ . '/../config/app.php';
require_once ROOT_PATH . '/includes/admin-check.php';   // cek akses sebelum ada output
require_once ROOT_PATH . '/includes/kontestan.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$k  = null;
if ($id) {
    $stmt = $pdo->prepare('SELECT * FROM kontestan WHERE id = ?');
    $stmt->execute([$id]);
    $k = $stmt->fetch() ?: null;
}
if (!$k) {
    set_flash('danger', 'Kontestan tidak ditemukan.');
    redirect('admin/kontestan.php');
}

$suara = jumlah_suara_kontestan($pdo, (int) $k['id']);

// Isian lama dari error validasi menimpa data dari database
$old  = pull_old();
$nilai = array_merge($k, $old);
$v     = fn($f) => e($nilai[$f] ?? '');

$judul     = 'Edit Kontestan';
$menuAktif = 'kontestan.php';
require_once ROOT_PATH . '/includes/admin-header.php';
?>

<div class="admin-kepala">
  <h1 class="judul-halaman">Edit Kontestan</h1>
  <a class="btn btn-outline-secondary btn-sm" href="<?= BASE_URL ?>/admin/kontestan.php">&larr; Kembali</a>
</div>

<form class="panel" method="post" enctype="multipart/form-data"
      action="<?= BASE_URL ?>/proses/kontestan-edit.php" data-sekali="Menyimpan...">
  <?= csrf_field() ?>
  <input type="hidden" name="id" value="<?= (int) $k['id'] ?>">

  <div class="row g-3">
    <div class="col-md-4">
      <label class="form-label" for="kategori">Kategori</label>
      <select class="form-select" id="kategori" name="kategori" required<?= $suara > 0 ? ' disabled' : '' ?>>
        <?php foreach (['Kakang', 'Senduk'] as $kat): ?>
          <option value="<?= $kat ?>"<?= ($nilai['kategori'] ?? '') === $kat ? ' selected' : '' ?>><?= $kat ?></option>
        <?php endforeach; ?>
      </select>
      <?php if ($suara > 0): ?>
        <div class="form-text">Terkunci karena sudah ada <?= $suara ?> suara.</div>
      <?php endif; ?>
    </div>

    <div class="col-md-4">
      <label class="form-label" for="nomor_peserta">Nomor peserta</label>
      <input class="form-control" id="nomor_peserta" name="nomor_peserta" maxlength="10"
             value="<?= $v('nomor_peserta') ?>" required>
      <div class="form-text">Unik per kategori.</div>
    </div>

    <div class="col-md-4">
      <label class="form-label" for="foto">Ganti foto (JPG, PNG, WebP, maks 2 MB)</label>
      <input class="form-control" type="file" id="foto" name="foto" accept="image/jpeg,image/png,image/webp">
      <div class="form-text">Kosongkan jika tidak ingin mengganti.</div>
    </div>

    <div class="col-12">
      <div class="teks-kecil teks-2 mb-1">Foto saat ini</div>
      <div style="max-width:120px">
        <?php if ($k['foto']): ?>
          <img class="foto" src="<?= UPLOAD_URL . '/' . rawurlencode($k['foto']) ?>" alt="Foto <?= e($k['nama']) ?>">
        <?php else: ?>
          <div class="foto-kosong">Belum ada</div>
        <?php endif; ?>
      </div>
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
    <button class="btn btn-primary" type="submit">Simpan perubahan</button>
    <a class="btn btn-outline-secondary" href="<?= BASE_URL ?>/admin/kontestan.php">Batal</a>
  </div>
</form>

<?php require_once ROOT_PATH . '/includes/admin-footer.php'; ?>