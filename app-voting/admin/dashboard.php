<?php
require_once __DIR__ . '/../config/app.php';
require_once ROOT_PATH . '/includes/klasemen.php';

$judul = 'Dashboard';
require_once ROOT_PATH . '/includes/admin-header.php';

// ---- Angka ringkasan ----
$totalPemilih  = (int) $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'pemilih'")->fetchColumn();
$sudahMemilih  = (int) $pdo->query('SELECT COUNT(DISTINCT user_id) FROM voting')->fetchColumn();
$totalSuara    = (int) $pdo->query('SELECT COUNT(*) FROM voting')->fetchColumn();
$totalKontes   = (int) $pdo->query('SELECT COUNT(*) FROM kontestan')->fetchColumn();
$persenIkut    = $totalPemilih > 0 ? round($sudahMemilih / $totalPemilih * 100) : 0;

// ---- Periode ----
$periode = periode_aktif($pdo);
$berikut = null;
if (!$periode) {
    $berikut = $pdo->query(
        "SELECT * FROM periode_voting
         WHERE status = 'Aktif' AND tanggal_mulai > NOW()
         ORDER BY tanggal_mulai ASC LIMIT 1"
    )->fetch() ?: null;
}

// ---- Klasemen 3 teratas per kategori ----
$klasemen = [];
foreach (['Kakang', 'Senduk'] as $kat) {
    $hasil = ambil_klasemen($pdo, $kat);
    $hasil['data'] = array_slice($hasil['data'], 0, 3);
    $klasemen[$kat] = $hasil;
}

// ---- Aktivitas terbaru ----
// Identitas pemilih sengaja tidak ditampilkan agar pilihan tetap rahasia.
$suaraBaru = $pdo->query(
    'SELECT v.created_at, v.kategori, k.nomor_peserta, k.nama
     FROM voting v
     JOIN kontestan k ON k.id = v.kontestan_id
     ORDER BY v.id DESC LIMIT 8'
)->fetchAll();

$pemilihBaru = $pdo->query(
    "SELECT nama, email, created_at
     FROM users
     WHERE role = 'pemilih'
     ORDER BY id DESC LIMIT 5"
)->fetchAll();
?>

<div class="admin-kepala">
  <h1 class="judul-halaman">Dashboard</h1>
  <div class="d-flex gap-2 flex-wrap">
    <a class="btn btn-primary btn-sm" href="<?= BASE_URL ?>/admin/tambah-kontestan.php">Tambah kontestan</a>
    <a class="btn btn-outline-secondary btn-sm" href="<?= BASE_URL ?>/pages/klasemen.php">Lihat klasemen publik</a>
  </div>
</div>

<div class="ringkas mb-4">
  <div>
    <div class="angka"><?= $totalPemilih ?></div>
    <div class="label">Pemilih terdaftar</div>
  </div>
  <div>
    <div class="angka"><?= $sudahMemilih ?></div>
    <div class="label">Sudah memilih (<?= $persenIkut ?>%)</div>
  </div>
  <div>
    <div class="angka"><?= $totalSuara ?></div>
    <div class="label">Total suara</div>
  </div>
  <div>
    <div class="angka"><?= $totalKontes ?></div>
    <div class="label">Kontestan</div>
  </div>
</div>

<div class="panel mb-4 d-flex flex-wrap align-items-center justify-content-between gap-2">
  <div>
    <div class="teks-kecil teks-2">Periode voting</div>
    <?php if ($periode): ?>
      <div class="status status-aktif"><?= e($periode['nama_periode']) ?> sedang berjalan</div>
      <div class="teks-kecil teks-2">Selesai <?= e(date('d-m-Y H:i', strtotime($periode['tanggal_selesai']))) ?> WIB</div>
    <?php elseif ($berikut): ?>
      <div class="status status-mati"><?= e($berikut['nama_periode']) ?> belum dimulai</div>
      <div class="teks-kecil teks-2">Mulai <?= e(date('d-m-Y H:i', strtotime($berikut['tanggal_mulai']))) ?> WIB</div>
    <?php else: ?>
      <div class="status status-mati">Tidak ada periode aktif</div>
      <div class="teks-kecil teks-2">Pemilih belum bisa memberikan suara.</div>
    <?php endif; ?>
  </div>
  <a class="btn btn-outline-secondary btn-sm" href="<?= BASE_URL ?>/admin/periode.php">Kelola periode</a>
</div>

<div class="row g-4 mb-4">
  <?php foreach ($klasemen as $kat => $hasil): ?>
    <div class="col-lg-6">
      <div class="d-flex justify-content-between align-items-baseline mb-2">
        <h2 class="h3 mb-0"><?= e($kat) ?></h2>
        <span class="teks-kecil teks-2"><?= (int) $hasil['total_suara'] ?> suara</span>
      </div>

      <?php if (!$hasil['data']): ?>
        <div class="panel teks-2">Belum ada kontestan.</div>
      <?php else: ?>
        <ul class="klasemen">
          <?php foreach ($hasil['data'] as $r): ?>
            <li class="klasemen-baris<?= $r['rank'] === 1 ? ' juara' : '' ?>">
              <div class="klasemen-rank"><?= $r['rank'] ?? '–' ?></div>
              <?php if ($r['foto_url']): ?>
                <img class="foto-kecil" src="<?= e($r['foto_url']) ?>" alt="">
              <?php else: ?>
                <div class="foto-kecil"></div>
              <?php endif; ?>
              <div>
                <div class="klasemen-nama"><?= e($r['nomor_peserta']) ?> &middot; <?= e($r['nama']) ?></div>
                <div class="bar"><span style="width:<?= (float) $r['persen'] ?>%"></span></div>
              </div>
              <div class="klasemen-suara"><?= (int) $r['suara'] ?></div>
            </li>
          <?php endforeach; ?>
        </ul>
        <a class="teks-kecil" href="<?= BASE_URL ?>/admin/hasil-voting.php">Lihat hasil lengkap &rarr;</a>
      <?php endif; ?>
    </div>
  <?php endforeach; ?>
</div>

<div class="row g-4">
  <div class="col-lg-6">
    <h2 class="h3 mb-2">Suara terbaru</h2>
    <div class="tabel-bungkus">
      <table class="tabel">
        <thead><tr><th>Waktu</th><th>Kategori</th><th>Kontestan</th></tr></thead>
        <tbody>
          <?php if (!$suaraBaru): ?>
            <tr><td colspan="3" class="teks-2">Belum ada suara masuk.</td></tr>
          <?php endif; ?>
          <?php foreach ($suaraBaru as $s): ?>
            <tr>
              <td><?= e(date('d-m H:i', strtotime($s['created_at']))) ?></td>
              <td><?= e($s['kategori']) ?></td>
              <td><?= e($s['nomor_peserta']) ?> &middot; <?= e($s['nama']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div class="col-lg-6">
    <h2 class="h3 mb-2">Pemilih baru</h2>
    <div class="tabel-bungkus">
      <table class="tabel">
        <thead><tr><th>Nama</th><th>Email</th><th>Daftar</th></tr></thead>
        <tbody>
          <?php if (!$pemilihBaru): ?>
            <tr><td colspan="3" class="teks-2">Belum ada pemilih terdaftar.</td></tr>
          <?php endif; ?>
          <?php foreach ($pemilihBaru as $p): ?>
            <tr>
              <td><?= e($p['nama']) ?></td>
              <td><?= e($p['email']) ?></td>
              <td><?= e(date('d-m H:i', strtotime($p['created_at']))) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <a class="teks-kecil" href="<?= BASE_URL ?>/admin/pemilih.php">Kelola pemilih &rarr;</a>
  </div>
</div>

<?php require_once ROOT_PATH . '/includes/admin-footer.php'; ?>