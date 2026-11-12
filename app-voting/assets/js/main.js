// 1. Cegah form terkirim dua kali.
//    Cara pakai: tambahkan atribut data-sekali pada <form>.
document.addEventListener('submit', function (e) {
  const form = e.target;
  if (!form.hasAttribute('data-sekali')) return;

  const tombol = form.querySelector('[type="submit"]');
  if (tombol) {
    tombol.dataset.teksAwal = tombol.textContent;
    tombol.disabled = true;
    tombol.textContent = form.dataset.sekali || 'Menyimpan...';
  }
});

// Kembalikan tombol saat pengguna menekan Back di browser
window.addEventListener('pageshow', function () {
  document.querySelectorAll('[data-sekali] [type="submit"]').forEach(function (tombol) {
    tombol.disabled = false;
    if (tombol.dataset.teksAwal) tombol.textContent = tombol.dataset.teksAwal;
  });
});

// 2. Menu samping admin di layar kecil
(function () {
  const tombol  = document.querySelector('[data-menu-admin]');
  const sidebar = document.getElementById('sidebarAdmin');
  const latar   = document.getElementById('latarAdmin');
  if (!tombol || !sidebar || !latar) return;

  function setMenu(buka) {
    sidebar.classList.toggle('terbuka', buka);
    latar.classList.toggle('terbuka', buka);
    tombol.setAttribute('aria-expanded', String(buka));
  }

  tombol.addEventListener('click', function () {
    setMenu(!sidebar.classList.contains('terbuka'));
  });
  latar.addEventListener('click', function () { setMenu(false); });
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') setMenu(false);
  });
})();