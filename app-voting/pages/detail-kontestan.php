<?php
require_once __DIR__ . '/../config/app.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$k  = null;
if ($id) {
    $stmt = $pdo->prepare('SELECT * FROM kontestan WHERE id = ?');
    $stmt->execute([$id]);
    $k = $stmt->fetch() ?: null;
}
if (!$k) {
    set_flash('danger', 'Kontestan tidak ditemukan.');
    redirect('pages/kontestan.php');
}

$lahir = [];
if ($k['tempat_lahir']) $lahir[] = $k['tempat_lahir'];
if ($k['tanggal_lahir']) $lahir[] = date('d-m-Y', strtotime($k['tanggal_lahir']));

$bagian = [
    'Visi'      => $k['visi'],
    'Misi'      => $k['misi'],
    'Prestasi'  => $k['prestasi'],
    'Deskripsi' => $k['deskripsi'],
];

$judul = $k['nama'];
require_once ROOT_PATH . '/includes/header.php';
?>

<p class="mb-4">
  <a href="<?= BASE_URL ?>/pages/kontestan.php?kategori=<?= e($k['kategori']) ?>">&larr; Kembali ke daftar <?= e($k['kategori']) ?></a>
</p>

<div class="row g-4 g-lg-5">
  <div class="col-sm-6 col-md-5 col-lg-4">
    <?php if ($k['foto']): ?>
      <img class="foto" src="<?= UPLOAD_URL . '/' . rawurlencode($k['foto']) ?>" alt="Foto <?= e($k['nama']) ?>">
    <?php else: ?>
      <div class="foto-kosong">Foto belum ada</div>
    <?php endif; ?>
  </div>

  <div class="col-md-7 col-lg-8">
    <div class="nomor-urut mb-2"><?= e($k['nomor_peserta']) ?></div>
    <h1 class="mb-4"><?= e($k['nama']) ?></h1>

    <dl class="daftar-data mb-4">
      <dt>Kategori</dt>
      <dd><?= e($k['kategori']) ?></dd>
      <dt>Asal</dt>
      <dd><?= e($k['asal_instansi']) ?></dd>
      <?php if ($lahir): ?>
        <dt>Tempat, tanggal lahir</dt>
        <dd><?= e(implode(', ', $lahir)) ?></dd>
      <?php endif; ?>
    </dl>

    <a class="btn btn-primary mb-4" href="<?= BASE_URL ?>/pages/voting.php?kategori=<?= e($k['kategori']) ?>">Vote kontestan ini</a>

    <?php foreach ($bagian as $label => $isi): ?>
      <?php if ($isi !== null && trim($isi) !== ''): ?>
        <h3 class="mt-4 mb-2"><?= e($label) ?></h3>
        <p><?= nl2br(e($isi)) ?></p>
      <?php endif; ?>
    <?php endforeach; ?>
  </div>
</div>

<?php require_once ROOT_PATH . '/includes/footer.php'; ?>