// Isi modal konfirmasi dengan data kontestan yang diklik
(function () {
  const modal = document.getElementById('modalVote');
  if (!modal) return;

  modal.addEventListener('show.bs.modal', function (e) {
    const tombol = e.relatedTarget;
    if (!tombol) return;
    document.getElementById('voteKontestanId').value = tombol.dataset.id || '';
    document.getElementById('voteNomor').textContent = tombol.dataset.nomor || '';
    document.getElementById('voteNama').textContent  = tombol.dataset.nama || '';
  });
})();