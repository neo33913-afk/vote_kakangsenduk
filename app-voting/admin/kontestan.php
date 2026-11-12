<?php
require_once __DIR__ . '/../config/app.php';

$judul         = 'Kontestan';
$skripTambahan = ['/assets/js/kontestan-admin.js'];
require_once ROOT_PATH . '/includes/admin-header.php';

$daftar = $pdo->query(
    'SELECT k.id, k.nomor_peserta, k.nama, k.kategori, k.foto, k.asal_instansi,
            COUNT(v.id) AS suara
     FROM kontestan k
     LEFT JOIN voting v ON v.kontestan_id = k.id
     GROUP BY k.id, k.nomor_peserta, k.nama, k.kategori, k.foto, k.asal_instansi
     ORDER BY k.kategori, CAST(k.nomor_peserta AS UNSIGNED), k.nomor_peserta'
)->fetchAll();
?>

<div class="admin-kepala">
  <h1 class="judul-halaman">Kontestan</h1>
  <a class="btn btn-primary btn-sm" href="<?= BASE_URL ?>/admin/tambah-kontestan.php">Tambah kontestan</a>
</div>

<div class="tabel-bungkus">
  <table class="tabel">
    <thead>
      <tr><th>Foto</th><th>No</th><th>Nama</th><th>Kategori</th><th>Asal</th><th>Suara</th><th>Aksi</th></tr>
    </thead>
    <tbody>
      <?php if (!$daftar): ?>
        <tr><td colspan="7" class="teks-2">Belum ada kontestan.</td></tr>
      <?php endif; ?>
      <?php foreach ($daftar as $k): ?>
        <tr>
          <td>
            <?php if ($k['foto']): ?>
              <img class="foto-kecil" src="<?= UPLOAD_URL . '/' . rawurlencode($k['foto']) ?>" alt="">
            <?php else: ?>
              <div class="foto-kecil"></div>
            <?php endif; ?>
          </td>
          <td><?= e($k['nomor_peserta']) ?></td>
          <td><?= e($k['nama']) ?></td>
          <td><?= e($k['kategori']) ?></td>
          <td><?= e($k['asal_instansi']) ?></td>
          <td><?= (int) $k['suara'] ?></td>
          <td class="text-nowrap">
            <a class="btn btn-outline-secondary btn-sm"
               href="<?= BASE_URL ?>/admin/edit-kontestan.php?id=<?= (int) $k['id'] ?>">Edit</a>
            <?php if ((int) $k['suara'] > 0): ?>
              <button type="button" class="btn btn-outline-danger btn-sm" disabled
                      title="Tidak bisa dihapus karena sudah punya suara">Hapus</button>
            <?php else: ?>
              <button type="button" class="btn btn-outline-danger btn-sm"
                      data-bs-toggle="modal" data-bs-target="#modalHapus"
                      data-id="<?= (int) $k['id'] ?>"
                      data-nama="<?= e($k['nama']) ?>">Hapus</button>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<div class="modal fade" id="modalHapus" tabindex="-1" aria-labelledby="judulModalHapus" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form class="modal-content" method="post" action="<?= BASE_URL ?>/proses/kontestan-hapus.php" data-sekali="Menghapus...">
      <?= csrf_field() ?>
      <input type="hidden" name="id" id="hapusId" value="">

      <div class="modal-header">
        <h2 class="modal-title fs-5" id="judulModalHapus">Hapus kontestan</h2>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <div class="modal-body">
        <p class="mb-2">Kamu akan menghapus <strong id="hapusNama"></strong>.</p>
        <p class="teks-2 teks-kecil mb-0">Data dan fotonya dihapus permanen dan tidak bisa dikembalikan.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Ya, hapus</button>
      </div>
    </form>
  </div>
</div>

<?php require_once ROOT_PATH . '/includes/admin-footer.php'; ?>