// Isi modal hapus pemilih dengan akun yang diklik
(function () {
  const modal = document.getElementById('modalHapusPemilih');
  if (!modal) return;

  modal.addEventListener('show.bs.modal', function (e) {
    const tombol = e.relatedTarget;
    if (!tombol) return;
    document.getElementById('hapusPemilihId').value = tombol.dataset.id || '';
    document.getElementById('hapusPemilihNama').textContent = tombol.dataset.nama || '';
  });
})();