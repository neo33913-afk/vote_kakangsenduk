  </div><!-- /.container -->
</main>

<footer class="situs-footer">
  <div class="container">
    &copy; <?= date('Y') ?> Pemilihan Kakang Senduk Ponorogo
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_URL ?>/assets/js/main.js"></script>
<?php foreach (($skripTambahan ?? []) as $skrip): ?>
  <script src="<?= BASE_URL . e($skrip) ?>"></script>
<?php endforeach; ?>
</body>
</html>