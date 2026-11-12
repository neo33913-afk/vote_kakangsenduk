<?php
require_once __DIR__ . '/../config/app.php';

$kategoriValid = ['Kakang', 'Senduk'];
$kategori = $_GET['kategori'] ?? 'Kakang';
if (!is_string($kategori) || !in_array($kategori, $kategoriValid, true)) {
    $kategori = 'Kakang';
}

$periode = periode_aktif($pdo);
$user    = current_user();
$login   = (bool) $user;
$admin   = is_admin();

// Pilihan pengguna di kategori ini (kalau sudah memilih)
$pilihanId = null;
if ($login && !$admin) {
    $cek = $pdo->prepare('SELECT kontestan_id FROM voting WHERE user_id = ? AND kategori = ?');
    $cek->execute([$user['id'], $kategori]);
    $pilihanId = $cek->fetchColumn();
    $pilihanId = $pilihanId !== false ? (int) $pilihanId : null;
}

$bisaVote = $periode && $login && !$admin && $pilihanId === null;

$stmt = $pdo->prepare(
    'SELECT id, nomor_peserta, nama, foto, asal_instansi
     FROM kontestan
     WHERE kategori = ?
     ORDER BY CAST(nomor_peserta AS UNSIGNED), nomor_peserta'
);
$stmt->execute([$kategori]);
$daftar = $stmt->fetchAll();

$judul = 'Voting ' . $kategori;
$skripTambahan = ['/assets/js/voting.js'];
require_once ROOT_PATH . '/includes/header.php';
?>

<h1 class="mb-1">Voting</h1>
<p class="teks-2 mb-4">Setiap akun dapat memberikan satu suara untuk tiap kategori. Suara tidak bisa diubah atau dibatalkan.</p>

<?php if (!$login): ?>
  <div class="alert alert-info">
    Kamu perlu <a href="<?= BASE_URL ?>/auth/login.php">masuk</a> atau
    <a href="<?= BASE_URL ?>/auth/register.php">daftar</a> untuk memberikan suara.
  </div>
<?php elseif ($admin): ?>
  <div class="alert alert-info">Akun admin tidak dapat memilih, agar hasil tetap netral.</div>
<?php elseif (!$periode): ?>
  <div class="alert alert-warning">Voting sedang tidak dibuka. Kamu tetap bisa melihat daftar kontestan.</div>
<?php elseif ($pilihanId !== null): ?>
  <div class="alert alert-success">Suaramu untuk kategori <?= e($kategori) ?> sudah tercatat. Terima kasih.</div>
<?php else: ?>
  <p class="status status-aktif mb-4">Voting dibuka sampai <?= e(date('d-m-Y H:i', strtotime($periode['tanggal_selesai']))) ?> WIB</p>
<?php endif; ?>

<div class="segmen">
  <?php foreach ($kategoriValid as $k): ?>
    <a href="<?= BASE_URL ?>/pages/voting.php?kategori=<?= $k ?>"
       class="<?= $k === $kategori ? 'aktif' : '' ?>"><?= e($k) ?></a>
  <?php endforeach; ?>
</div>

<?php if (!$daftar): ?>
  <div class="panel teks-2">Belum ada kontestan untuk kategori <?= e($kategori) ?>.</div>
<?php else: ?>
  <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-4">
    <?php foreach ($daftar as $k): $pilihan = ($pilihanId === (int) $k['id']); ?>
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

          <?php if ($bisaVote): ?>
            <button type="button" class="btn btn-primary btn-sm"
                    data-bs-toggle="modal" data-bs-target="#modalVote"
                    data-id="<?= (int) $k['id'] ?>"
                    data-nomor="<?= e($k['nomor_peserta']) ?>"
                    data-nama="<?= e($k['nama']) ?>">Vote</button>
          <?php elseif ($pilihan): ?>
            <button type="button" class="btn btn-success btn-sm" disabled>Pilihanmu</button>
          <?php elseif (!$login && $periode): ?>
            <a class="btn btn-primary btn-sm" href="<?= BASE_URL ?>/auth/login.php">Masuk</a>
          <?php else: ?>
            <button type="button" class="btn btn-primary btn-sm" disabled>Vote</button>
          <?php endif; ?>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<?php if ($bisaVote): ?>
<div class="modal fade" id="modalVote" tabindex="-1" aria-labelledby="judulModalVote" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form class="modal-content" method="post" action="<?= BASE_URL ?>/proses/simpan-voting.php" data-sekali="Mengirim suara...">
      <?= csrf_field() ?>
      <input type="hidden" name="kontestan_id" id="voteKontestanId" value="">

      <div class="modal-header">
        <h2 class="modal-title fs-5" id="judulModalVote">Konfirmasi suara</h2>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <div class="modal-body">
        <p class="mb-2">Kamu akan memilih:</p>
        <p class="mb-3"><span class="nomor-urut nomor-urut-kecil" id="voteNomor"></span>
          <strong id="voteNama" class="ms-2"></strong></p>
        <p class="teks-2 teks-kecil mb-0">Kategori <?= e($kategori) ?>. Suara tidak bisa diubah atau dibatalkan setelah dikirim.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Kirim suara</button>
      </div>
    </form>
  </div>
</div>
<?php endif; ?>

<?php require_once ROOT_PATH . '/includes/footer.php'; ?>