<?php
require_once __DIR__ . '/../config/app.php';
require_once ROOT_PATH . '/includes/admin-check.php';
require_once ROOT_PATH . '/includes/klasemen.php';

$kategoriValid = ['Kakang', 'Senduk'];
$kategori = $_GET['kategori'] ?? 'Kakang';
if (!is_string($kategori) || !in_array($kategori, $kategoriValid, true)) {
    $kategori = 'Kakang';
}

$hasil = ambil_klasemen($pdo, $kategori);

// ---- Unduh CSV ----
if (($_GET['export'] ?? '') === 'csv') {
    // Cegah sel yang diawali = + - @ dibaca sebagai rumus oleh Excel
    $aman = fn($s) => preg_match('/^[=+\-@\t\r]/', (string) $s) ? "'" . $s : (string) $s;

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="hasil-' . strtolower($kategori) . '-' . date('Ymd-His') . '.csv"');

    $out = fopen('php://output', 'w');
    fwrite($out, "\xEF\xBB\xBF");   // BOM agar Excel membaca UTF-8
    fputcsv($out, ['Peringkat', 'Nomor', 'Nama', 'Asal', 'Suara', 'Persen'], ',', '"', '');
    foreach ($hasil['data'] as $r) {
        fputcsv($out, [
            $r['rank'] ?? '',
            $aman($r['nomor_peserta']),
            $aman($r['nama']),
            $aman($r['asal_instansi']),
            $r['suara'],
            $r['persen'],
        ], ',', '"', '');
    }
    fclose($out);
    exit;
}

$totalPemilih = (int) $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'pemilih'")->fetchColumn();
$persenIkut   = $totalPemilih > 0 ? round($hasil['total_suara'] / $totalPemilih * 100) : 0;

$judul = 'Hasil Voting';
require_once ROOT_PATH . '/includes/admin-header.php';
?>

<div class="admin-kepala">
  <h1 class="judul-halaman">Hasil Voting</h1>
  <a class="btn btn-outline-secondary btn-sm"
     href="<?= BASE_URL ?>/admin/hasil-voting.php?kategori=<?= e($kategori) ?>&amp;export=csv">Unduh CSV</a>
</div>

<div class="segmen">
  <?php foreach ($kategoriValid as $k): ?>
    <a href="<?= BASE_URL ?>/admin/hasil-voting.php?kategori=<?= $k ?>"
       class="<?= $k === $kategori ? 'aktif' : '' ?>"><?= e($k) ?></a>
  <?php endforeach; ?>
</div>

<div class="ringkas mb-4">
  <div>
    <div class="angka"><?= (int) $hasil['total_suara'] ?></div>
    <div class="label">Suara <?= e($kategori) ?></div>
  </div>
  <div>
    <div class="angka"><?= $totalPemilih ?></div>
    <div class="label">Pemilih terdaftar</div>
  </div>
  <div>
    <div class="angka"><?= $persenIkut ?>%</div>
    <div class="label">Partisipasi</div>
  </div>
</div>

<div class="tabel-bungkus">
  <table class="tabel">
    <thead>
      <tr><th>Rank</th><th>Foto</th><th>No</th><th>Nama</th><th>Asal</th><th>Suara</th><th style="min-width:140px">Persen</th></tr>
    </thead>
    <tbody>
      <?php if (!$hasil['data']): ?>
        <tr><td colspan="7" class="teks-2">Belum ada kontestan untuk kategori <?= e($kategori) ?>.</td></tr>
      <?php endif; ?>
      <?php foreach ($hasil['data'] as $r): ?>
        <tr>
          <td><strong><?= $r['rank'] ?? '–' ?></strong></td>
          <td>
            <?php if ($r['foto_url']): ?>
              <img class="foto-kecil" src="<?= e($r['foto_url']) ?>" alt="">
            <?php else: ?>
              <div class="foto-kecil"></div>
            <?php endif; ?>
          </td>
          <td><?= e($r['nomor_peserta']) ?></td>
          <td><?= e($r['nama']) ?></td>
          <td><?= e($r['asal_instansi']) ?></td>
          <td><strong><?= (int) $r['suara'] ?></strong></td>
          <td>
            <?= e($r['persen']) ?>%
            <div class="bar"><span style="width:<?= (float) $r['persen'] ?>%"></span></div>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<p class="teks-kecil teks-2 mt-3 mb-0">Suara sama menghasilkan peringkat sama (contoh: 1, 1, 3). Persen dihitung dari total suara kategori ini.</p>

<?php require_once ROOT_PATH . '/includes/admin-footer.php'; ?>