<?php
require_once __DIR__ . '/../config/app.php';
set_flash('success', 'Ini contoh pesan sukses.');
$judul = 'Tes Tampilan';
require_once ROOT_PATH . '/includes/header.php';
?>

<h1 class="mb-3">Judul Halaman Contoh</h1>
<p class="teks-2 mb-4">Teks sekunder untuk deskripsi singkat.</p>

<div class="mb-4 d-flex gap-2 flex-wrap">
  <button class="btn btn-primary">Utama</button>
  <button class="btn btn-outline-secondary">Sekunder</button>
  <button class="btn btn-outline-danger">Hapus</button>
  <button class="btn btn-success">Aktifkan</button>
  <button class="btn btn-primary" disabled>Sudah memilih</button>
</div>

<div class="segmen">
  <a href="#" class="aktif">Kakang</a>
  <a href="#">Senduk</a>
</div>

<div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-4 mb-5">
  <div class="col">
    <div class="foto-kosong">Foto belum ada</div>
    <div class="nomor-urut mt-2">01</div>
    <div class="kontestan-nama">Nama Kontestan</div>
    <div class="kontestan-meta">SMKN 1 Ponorogo</div>
    <div class="kontestan-aksi">
      <a href="#" class="btn btn-outline-secondary btn-sm">Detail</a>
      <a href="#" class="btn btn-primary btn-sm">Vote</a>
    </div>
  </div>
</div>

<ul class="klasemen mb-5">
  <li class="klasemen-baris juara">
    <div class="klasemen-rank">1</div>
    <div class="foto-kecil"></div>
    <div><div class="klasemen-nama">Kakang 01</div><div class="bar"><span style="width:75%"></span></div></div>
    <div class="klasemen-suara">350</div>
  </li>
  <li class="klasemen-baris">
    <div class="klasemen-rank">2</div>
    <div class="foto-kecil"></div>
    <div><div class="klasemen-nama">Kakang 02</div><div class="bar"><span style="width:40%"></span></div></div>
    <div class="klasemen-suara">190</div>
  </li>
</ul>

<div class="row g-4">
  <div class="col-md-5">
    <form class="panel" data-sekali onsubmit="return false">
      <label class="form-label" for="email">Email</label>
      <input class="form-control mb-3" id="email" type="email" placeholder="nama@contoh.com">
      <button class="btn btn-primary" type="submit">Kirim</button>
    </form>
  </div>
  <div class="col-md-7">
    <div class="tabel-bungkus">
      <table class="tabel">
        <thead><tr><th>No</th><th>Nama</th><th>Status</th></tr></thead>
        <tbody>
          <tr><td>01</td><td>Kontestan Kakang 1</td><td class="status status-aktif">Aktif</td></tr>
          <tr><td>02</td><td>Kontestan Kakang 2</td><td class="status status-mati">Nonaktif</td></tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php require_once ROOT_PATH . '/includes/footer.php'; ?>