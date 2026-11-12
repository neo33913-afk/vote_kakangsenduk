// Pembaruan klasemen otomatis: hanya mengganti angka, bar, dan urutan baris.
(function () {
  const daftar = document.getElementById('daftarKlasemen');
  if (!daftar) return;

  const kategori = daftar.dataset.kategori;
  const api      = daftar.dataset.api;
  const JEDA     = 10000;   // 10 detik
  let berjalan   = false;

  function perbarui(data) {
    const baris = new Map();
    daftar.querySelectorAll('.klasemen-baris').forEach(function (li) {
      baris.set(li.dataset.id, li);
    });

    // Ada kontestan baru atau yang dihapus: muat ulang halaman
    if (data.data.length !== baris.size ||
        data.data.some(function (r) { return !baris.has(String(r.id)); })) {
      window.location.reload();
      return;
    }

    data.data.forEach(function (r) {
      const li = baris.get(String(r.id));
      li.classList.toggle('juara', r.rank === 1);
      li.querySelector('.klasemen-rank').textContent  = r.rank === null ? '–' : r.rank;
      li.querySelector('.klasemen-suara').textContent = r.suara;
      li.querySelector('.bar > span').style.width     = r.persen + '%';
      daftar.appendChild(li);   // memindahkan baris sesuai urutan terbaru
    });

    document.getElementById('totalSuara').textContent  = data.total_suara;
    document.getElementById('waktuUpdate').textContent = data.diperbarui;
  }

  async function ambil() {
    if (berjalan || document.hidden) return;
    berjalan = true;
    try {
      const res = await fetch(api + '?kategori=' + encodeURIComponent(kategori), {
        headers: { 'Accept': 'application/json' },
        cache: 'no-store'
      });
      if (!res.ok) throw new Error('HTTP ' + res.status);
      perbarui(await res.json());
    } catch (err) {
      // Gagal sesekali tidak masalah; coba lagi di putaran berikutnya
    } finally {
      berjalan = false;
    }
  }

  setInterval(ambil, JEDA);
  document.addEventListener('visibilitychange', function () {
    if (!document.hidden) ambil();
  });
})();