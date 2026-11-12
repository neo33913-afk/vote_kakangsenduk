<?php
require_once __DIR__ . '/../config/app.php';
require_once ROOT_PATH . '/includes/klasemen.php';

$kategori = $_GET['kategori'] ?? 'Kakang';
if (!is_string($kategori) || !in_array($kategori, KATEGORI_VALID, true)) {
    $kategori = 'Kakang';
}

$hasil = ambil_klasemen($pdo, $kategori);

$judul         = 'Klasemen ' . $kategori;
$skripTambahan = ['/assets/js/klasemen.js'];
require_once ROOT_PATH . '/includes/header.php';
?>

<h1 class="mb-1">Klasemen</h1>
<p class="teks-2 mb-4">Perolehan suara sementara. Angka diperbarui otomatis.</p>

<div class="segmen">
  <?php foreach (KATEGORI_VALID as $k): ?>
    <a href="<?= BASE_URL ?>/pages/klasemen.php?kategori=<?= $k ?>"
       class="<?= $k === $kategori ? 'aktif' : '' ?>"><?= e($k) ?></a>
  <?php endforeach; ?>
</div>

<div class="d-flex justify-content-between flex-wrap gap-2 teks-kecil teks-2 mb-3">
  <span><span id="totalSuara"><?= (int) $hasil['total_suara'] ?></span> suara masuk</span>
  <span>Diperbarui <span id="waktuUpdate"><?= e($hasil['diperbarui']) ?></span></span>
</div>

<?php if (!$hasil['data']): ?>
  <div class="panel teks-2">Belum ada kontestan untuk kategori <?= e($kategori) ?>.</div>
<?php else: ?>
  <ul class="klasemen" id="daftarKlasemen"
      data-kategori="<?= e($kategori) ?>"
      data-api="<?= BASE_URL ?>/api/klasemen.php">
    <?php foreach ($hasil['data'] as $r): ?>
      <li class="klasemen-baris<?= $r['rank'] === 1 ? ' juara' : '' ?>" data-id="<?= (int) $r['id'] ?>">
        <div class="klasemen-rank"><?= $r['rank'] ?? '–' ?></div>
        <?php if ($r['foto_url']): ?>
          <img class="foto-kecil" src="<?= e($r['foto_url']) ?>" alt="Foto <?= e($r['nama']) ?>" loading="lazy">
        <?php else: ?>
          <div class="foto-kecil"></div>
        <?php endif; ?>
        <div>
          <a class="klasemen-nama text-decoration-none" style="color:inherit"
             href="<?= BASE_URL ?>/pages/detail-kontestan.php?id=<?= (int) $r['id'] ?>"><?= e($r['nomor_peserta']) ?> &middot; <?= e($r['nama']) ?></a>
          <div class="kontestan-meta"><?= e($r['asal_instansi']) ?></div>
          <div class="bar"><span style="width:<?= (float) $r['persen'] ?>%"></span></div>
        </div>
        <div class="klasemen-suara"><?= (int) $r['suara'] ?></div>
      </li>
    <?php endforeach; ?>
  </ul>

  <p class="teks-kecil teks-2 mt-3 mb-0" id="infoKlasemen">
    <?= $hasil['total_suara'] > 0
        ? 'Suara sama menghasilkan peringkat sama (contoh: 1, 1, 3).'
        : 'Belum ada suara masuk. Peringkat muncul setelah suara pertama.' ?>
  </p>
<?php endif; ?>

<?php require_once ROOT_PATH . '/includes/footer.php'; ?>