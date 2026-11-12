<?php
// Menampilkan pesan satu kali (hasil set_flash) sebagai alert
$flash = get_flash();
if ($flash):
    $tipe = in_array($flash['type'], ['success', 'danger', 'warning', 'info'], true) ? $flash['type'] : 'info';
?>
<div class="alert alert-<?= $tipe ?> alert-dismissible fade show" role="alert">
  <?= e($flash['message']) ?>
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
</div>
<?php endif; ?>