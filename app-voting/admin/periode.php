<?php
require_once __DIR__ . '/../config/app.php';
require_once ROOT_PATH . '/includes/admin-check.php';

$old    = pull_old();
$editId = filter_input(INPUT_GET, 'edit', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$edit   = null;
if ($editId) {
    $stmt = $pdo->prepare('SELECT * FROM periode_voting WHERE id = ?');
    $stmt->execute([$editId]);
    $edit = $stmt->fetch() ?: null;
}

// Nilai form: data periode yang diedit, lalu isian lama kalau ada error
$f = array_merge(
    ['id' => '', 'nama_periode' => '', 'tanggal_mulai' => '', 'tanggal_selesai' => ''],
    $edit ?: [],
    $old
);
$untukInput = fn($s) => $s ? str_replace(' ', 'T', substr((string) $s, 0, 16)) : '';

$daftar = $pdo->query('SELECT * FROM periode_voting ORDER BY tanggal_mulai DESC, id DESC')->fetchAll();

$judul = 'Periode Voting';
require_once ROOT_PATH . '/includes/admin-header.php';
?>

<div class="admin-kepala">
  <h1 class="judul-halaman">Periode Voting</h1>
</div>

<form class="panel mb-4" method="post" action="<?= BASE_URL ?>/proses/periode-simpan.php" data-sekali="Menyimpan...">
  <?= csrf_field() ?>
  <input type="hidden" name="id" value="<?= e($f['id']) ?>">

  <h2 class="h3 mb-3"><?= $f['id'] !== '' ? 'Edit periode' : 'Tambah periode' ?></h2>

  <div class="row g-3">
    <div class="col-md-4">
      <label class="form-label" for="nama_periode">Nama periode</label>
      <input class="form-control" id="nama_periode" name="nama_periode" maxlength="100"
             value="<?= e($f['nama_periode']) ?>" placeholder="Voting Babak Final" required>
    </div>
    <div class="col-md-4">
      <label class="form-label" for="tanggal_mulai">Mulai (WIB)</label>
      <input class="form-control" type="datetime-local" id="tanggal_mulai" name="tanggal_mulai"
             value="<?= e($untukInput($f['tanggal_mulai'])) ?>" required>
    </div>
    <div class="col-md-4">
      <label class="form-label" for="tanggal_selesai">Selesai (WIB)</label>
      <input class="form-control" type="datetime-local" id="tanggal_selesai" name="tanggal_selesai"
             value="<?= e($untukInput($f['tanggal_selesai'])) ?>" required>
    </div>
  </div>

  <div class="d-flex gap-2 mt-4">
    <button class="btn btn-primary" type="submit">Simpan</button>
    <?php if ($f['id'] !== ''): ?>
      <a class="btn btn-outline-secondary" href="<?= BASE_URL ?>/admin/periode.php">Batal</a>
    <?php endif; ?>
  </div>
  <div class="form-text mt-2">Periode baru tersimpan dalam keadaan nonaktif. Aktifkan lewat tombol di tabel.</div>
</form>

<div class="tabel-bungkus">
  <table class="tabel">
    <thead>
      <tr><th>Nama</th><th>Mulai</th><th>Selesai</th><th>Status</th><th>Aksi</th></tr>
    </thead>
    <tbody>
      <?php if (!$daftar): ?>
        <tr><td colspan="5" class="teks-2">Belum ada periode.</td></tr>
      <?php endif; ?>
      <?php foreach ($daftar as $p):
        $mulai   = strtotime($p['tanggal_mulai']);
        $selesai = strtotime($p['tanggal_selesai']);
        $now     = time();
        if ($p['status'] !== 'Aktif') {
            $label = 'Nonaktif';  $kelas = 'status-mati';
        } elseif ($now < $mulai) {
            $label = 'Aktif, terjadwal';  $kelas = 'status-mati';
        } elseif ($now <= $selesai) {
            $label = 'Berjalan';  $kelas = 'status-aktif';
        } else {
            $label = 'Aktif, sudah berakhir';  $kelas = 'status-mati';
        }
      ?>
        <tr>
          <td><?= e($p['nama_periode']) ?></td>
          <td><?= e(date('d-m-Y H:i', $mulai)) ?></td>
          <td><?= e(date('d-m-Y H:i', $selesai)) ?></td>
          <td class="status <?= $kelas ?>"><?= e($label) ?></td>
          <td class="text-nowrap">
            <a class="btn btn-outline-secondary btn-sm"
               href="<?= BASE_URL ?>/admin/periode.php?edit=<?= (int) $p['id'] ?>">Edit</a>
            <form class="d-inline" method="post" action="<?= BASE_URL ?>/proses/periode-toggle.php" data-sekali="Memproses...">
              <?= csrf_field() ?>
              <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
              <?php if ($p['status'] === 'Aktif'): ?>
                <input type="hidden" name="aksi" value="nonaktifkan">
                <button class="btn btn-outline-secondary btn-sm" type="submit">Nonaktifkan</button>
              <?php else: ?>
                <input type="hidden" name="aksi" value="aktifkan">
                <button class="btn btn-success btn-sm" type="submit">Aktifkan</button>
              <?php endif; ?>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php require_once ROOT_PATH . '/includes/admin-footer.php'; ?>