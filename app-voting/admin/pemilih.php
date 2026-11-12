<?php
require_once __DIR__ . '/../config/app.php';
require_once ROOT_PATH . '/includes/admin-check.php';

$perHalaman = 20;

$q = is_string($_GET['q'] ?? null) ? mb_substr(trim($_GET['q']), 0, 100) : '';
$where  = "u.role = 'pemilih'";
$param  = [];
if ($q !== '') {
    $like   = '%' . addcslashes($q, '\\%_') . '%';
    $where .= ' AND (u.nama LIKE ? OR u.email LIKE ?)';
    $param  = [$like, $like];
}

$hitung = $pdo->prepare("SELECT COUNT(*) FROM users u WHERE $where");
$hitung->execute($param);
$total = (int) $hitung->fetchColumn();

$jumlahHal = max(1, (int) ceil($total / $perHalaman));
$hal       = min($jumlahHal, max(1, (int) ($_GET['hal'] ?? 1)));
$offset    = ($hal - 1) * $perHalaman;

$stmt = $pdo->prepare(
    "SELECT u.id, u.nama, u.email, u.no_hp, u.created_at,
            SUM(v.kategori = 'Kakang') AS kakang,
            SUM(v.kategori = 'Senduk') AS senduk,
            COUNT(v.id) AS suara
     FROM users u
     LEFT JOIN voting v ON v.user_id = u.id
     WHERE $where
     GROUP BY u.id, u.nama, u.email, u.no_hp, u.created_at
     ORDER BY u.id DESC
     LIMIT $perHalaman OFFSET $offset"
);
$stmt->execute($param);
$daftar = $stmt->fetchAll();

$urlHal = fn(int $n) => BASE_URL . '/admin/pemilih.php?' . http_build_query(array_filter(['q' => $q, 'hal' => $n > 1 ? $n : null]));

$judul         = 'Pemilih';
$skripTambahan = ['/assets/js/pemilih-admin.js'];
require_once ROOT_PATH . '/includes/admin-header.php';
?>

<div class="admin-kepala">
  <h1 class="judul-halaman">Pemilih <span class="teks-2 teks-kecil">(<?= $total ?>)</span></h1>
  <form class="d-flex gap-2" method="get" action="<?= BASE_URL ?>/admin/pemilih.php">
    <input class="form-control form-control-sm" type="search" name="q" value="<?= e($q) ?>"
           placeholder="Cari nama atau email" maxlength="100" aria-label="Cari pemilih">
    <button class="btn btn-outline-secondary btn-sm" type="submit">Cari</button>
  </form>
</div>

<div class="tabel-bungkus">
  <table class="tabel">
    <thead>
      <tr><th>Nama</th><th>Email</th><th>No. HP</th><th>Daftar</th><th>Kakang</th><th>Senduk</th><th>Aksi</th></tr>
    </thead>
    <tbody>
      <?php if (!$daftar): ?>
        <tr><td colspan="7" class="teks-2"><?= $q !== '' ? 'Tidak ada pemilih yang cocok.' : 'Belum ada pemilih terdaftar.' ?></td></tr>
      <?php endif; ?>
      <?php foreach ($daftar as $p): ?>
        <tr>
          <td><?= e($p['nama']) ?></td>
          <td><?= e($p['email']) ?></td>
          <td><?= e($p['no_hp']) ?></td>
          <td><?= e(date('d-m-Y H:i', strtotime($p['created_at']))) ?></td>
          <td class="<?= (int) $p['kakang'] > 0 ? 'status status-aktif' : 'teks-2' ?>"><?= (int) $p['kakang'] > 0 ? 'Sudah' : 'Belum' ?></td>
          <td class="<?= (int) $p['senduk'] > 0 ? 'status status-aktif' : 'teks-2' ?>"><?= (int) $p['senduk'] > 0 ? 'Sudah' : 'Belum' ?></td>
          <td class="text-nowrap">
            <?php if ((int) $p['suara'] > 0): ?>
              <button type="button" class="btn btn-outline-danger btn-sm" disabled
                      title="Tidak bisa dihapus karena sudah memberikan suara">Hapus</button>
            <?php else: ?>
              <button type="button" class="btn btn-outline-danger btn-sm"
                      data-bs-toggle="modal" data-bs-target="#modalHapusPemilih"
                      data-id="<?= (int) $p['id'] ?>"
                      data-nama="<?= e($p['nama']) ?>">Hapus</button>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php if ($jumlahHal > 1): ?>
  <nav class="d-flex justify-content-between align-items-center mt-3" aria-label="Halaman">
    <?php if ($hal > 1): ?>
      <a class="btn btn-outline-secondary btn-sm" href="<?= e($urlHal($hal - 1)) ?>">&larr; Sebelumnya</a>
    <?php else: ?><span></span><?php endif; ?>
    <span class="teks-kecil teks-2">Halaman <?= $hal ?> dari <?= $jumlahHal ?></span>
    <?php if ($hal < $jumlahHal): ?>
      <a class="btn btn-outline-secondary btn-sm" href="<?= e($urlHal($hal + 1)) ?>">Berikutnya &rarr;</a>
    <?php else: ?><span></span><?php endif; ?>
  </nav>
<?php endif; ?>

<div class="modal fade" id="modalHapusPemilih" tabindex="-1" aria-labelledby="judulModalHapusPemilih" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form class="modal-content" method="post" action="<?= BASE_URL ?>/proses/pemilih-hapus.php" data-sekali="Menghapus...">
      <?= csrf_field() ?>
      <input type="hidden" name="id" id="hapusPemilihId" value="">

      <div class="modal-header">
        <h2 class="modal-title fs-5" id="judulModalHapusPemilih">Hapus pemilih</h2>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <div class="modal-body">
        <p class="mb-2">Kamu akan menghapus akun <strong id="hapusPemilihNama"></strong>.</p>
        <p class="teks-2 teks-kecil mb-0">Akun dihapus permanen dan tidak bisa dikembalikan.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Ya, hapus</button>
      </div>
    </form>
  </div>
</div>

<?php require_once ROOT_PATH . '/includes/admin-footer.php'; ?>