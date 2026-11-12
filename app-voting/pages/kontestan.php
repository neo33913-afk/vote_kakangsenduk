<?php
require_once __DIR__ . '/../config/app.php';

$kategoriValid = ['Kakang', 'Senduk'];
$kategori = $_GET['kategori'] ?? 'Kakang';
if (!is_string($kategori) || !in_array($kategori, $kategoriValid, true)) {
    $kategori = 'Kakang';
}

$stmt = $pdo->prepare(
    'SELECT id, nomor_peserta, nama, foto, asal_instansi
     FROM kontestan
     WHERE kategori = ?
     ORDER BY CAST(nomor_peserta AS UNSIGNED), nomor_peserta'
);
$stmt->execute([$kategori]);
$daftar = $stmt->fetchAll();

$judul = 'Kontestan ' . $kategori;
require_once ROOT_PATH . '/includes/header.php';
?>

<h1 class="mb-1">Kontestan</h1>
<p class="teks-2 mb-4">Pilih kategori untuk melihat daftar peserta.</p>

<div class="segmen">
  <?php foreach ($kategoriValid as $k): ?>
    <a href="<?= BASE_URL ?>/pages/kontestan.php?kategori=<?= $k ?>"
       class="<?= $k === $kategori ? 'aktif' : '' ?>"><?= e($k) ?></a>
  <?php endforeach; ?>
</div>

<?php if (!$daftar): ?>
  <div class="panel teks-2">Belum ada kontestan untuk kategori <?= e($kategori) ?>.</div>
<?php else: ?>
  <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-4">
    <?php foreach ($daftar as $k): ?>
      <div class="col">
        <a href="<?= BASE_URL ?>/pages/detail-kontestan.php?id=<?= (int) $k['id'] ?>" aria-label="Detail <?= e($k['nama']) ?>">
          <?php if ($k['foto']): ?>
            <img class="foto" src="<?= UPLOAD_URL . '/' . rawurlencode($k['foto']) ?>"
                 alt="Foto <?= e($k['nama']) ?>" loading="lazy">
          <?php else: ?>
            <div class="foto-kosong">Foto belum ada</div>
          <?php endif; ?>
        </a>
        <div class="nomor-urut mt-2"><?= e($k['nomor_peserta']) ?></div>
        <div class="kontestan-nama"><?= e($k['nama']) ?></div>
        <div class="kontestan-meta"><?= e($k['asal_instansi']) ?></div>
        <div class="kontestan-aksi">
          <a href="<?= BASE_URL ?>/pages/detail-kontestan.php?id=<?= (int) $k['id'] ?>"
             class="btn btn-outline-secondary btn-sm">Detail</a>
          <a href="<?= BASE_URL ?>/pages/voting.php?kategori=<?= e($kategori) ?>"
             class="btn btn-primary btn-sm">Vote</a>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<?php require_once ROOT_PATH . '/includes/footer.php'; ?>