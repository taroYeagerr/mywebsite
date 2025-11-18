<?php
session_start();
require_once 'classes/Book.php';

$b = new Book();
$books = $b->getAll();
?>

<link rel="stylesheet" href="assets/bootstrap.min.css">

<div class="container mt-5">
<h2>Daftar Buku Perpustakaan</h2>
<div class="row">
<?php while ($row = $books->fetch_assoc()) { ?>
  <div class="col-md-3 mb-4">
    <div class="card h-100 shadow">
      <?php
        // Cek apakah gambar cover benar-benar ada di folder
        if ($row['cover'] && file_exists($row['cover'])) { ?>
          <img src="<?= $row['cover'] ?>" class="card-img-top" alt="Cover <?= htmlentities($row['title']) ?>"
               style="height:220px;object-fit:cover;">
      <?php } else { ?>
          <div class="card-img-top bg-light d-flex align-items-center justify-content-center"
               style="height:220px;">Tidak Ada Gambar</div>
      <?php } ?>
      <div class="card-body">
        <h5 class="card-title text-center"><?= htmlentities($row['title']); ?></h5>
        <hr>
        <p class="small">
          <strong>Penulis:</strong> <?= htmlentities($row['author']); ?><br>
          <strong>Penerbit:</strong> <?= htmlentities($row['publisher']); ?><br>
          <strong>Tahun:</strong> <?= $row['year']; ?><br>
          <strong>Stok:</strong> <?= $row['stock']; ?>
        </p>
      </div>
    </div>
  </div>
<?php } ?>
</div>
<a href="dashboard.php" class="btn btn-secondary mt-3">Kembali</a>
</div>