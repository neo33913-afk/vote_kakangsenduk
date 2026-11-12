<?php
require_once __DIR__ . '/../config/app.php';

$periode = periode_aktif($pdo);
$berikut = null;
if (!$periode) {
    $berikut = $pdo->query(
        "SELECT * FROM periode_voting
         WHERE status = 'Aktif' AND tanggal_mulai > NOW()
         ORDER BY tanggal_mulai ASC LIMIT 1"
    )->fetch() ?: null;
}

$jumlah = ['Kakang' => 0, 'Senduk' => 0];
foreach ($pdo->query('SELECT kategori, COUNT(*) AS n FROM kontestan GROUP BY kategori') as $r) {
    $jumlah[$r['kategori']] = (int) $r['n'];
}
$totalSuara = (int) $pdo->query('SELECT COUNT(*) FROM voting')->fetchColumn();

$judul = 'Beranda';
require_once ROOT_PATH . '/includes/header.php';
?>

<div class="row">
  <div class="col-lg-8">
    <p class="merek-sub mb-2">Ponorogo</p>
    <h1 class="mb-3">Pemilihan Kakang Senduk</h1>
    <p class="teks-2 mb-4">Kenali para kontestan, baca visi dan misinya, lalu berikan suaramu.</p>

    <p class="mb-4">
      <?php if ($periode): ?>
        <span class="status status-aktif">Voting dibuka</span>
        <span class="teks-2">sampai <?= e(date('d-m-Y H:i', strtotime($periode['tanggal_selesai']))) ?> WIB</span>
      <?php elseif ($berikut): ?>
        <span class="status status-mati">Voting belum dibuka</span>
        <span class="teks-2">mulai <?= e(date('d-m-Y H:i', strtotime($berikut['tanggal_mulai']))) ?> WIB</span>
      <?php else: ?>
        <span class="status status-mati">Voting sedang ditutup</span>
      <?php endif; ?>
    </p>

    <div class="d-flex gap-2 flex-wrap mb-5">
      <a class="btn btn-primary" href="<?= BASE_URL ?>/pages/kontestan.php">Lihat Kontestan</a>
      <a class="btn btn-outline-secondary" href="<?= BASE_URL ?>/pages/klasemen.php">Lihat Klasemen</a>
    </div>
  </div>
</div>

<div class="ringkas">
  <div>
    <div class="angka"><?= $jumlah['Kakang'] ?></div>
    <div class="label">Kontestan Kakang</div>
  </div>
  <div>
    <div class="angka"><?= $jumlah['Senduk'] ?></div>
    <div class="label">Kontestan Senduk</div>
  </div>
  <div>
    <div class="angka"><?= $totalSuara ?></div>
    <div class="label">Suara masuk</div>
  </div>
</div>

<?php require_once ROOT_PATH . '/includes/footer.php'; ?>