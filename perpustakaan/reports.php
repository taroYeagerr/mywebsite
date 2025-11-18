<?php
session_start();
require_once 'db.php';

$user_id = $_SESSION['user']['id'] ?? 1; // Ganti ini sesuai sistem login yang kamu pakai

$db = (new Database())->conn;

// Proses pengembalian buku
if (isset($_POST['kembalikan'])) {
    $borrow_id = $_POST['borrow_id'];
    // Update status pinjaman dan tanggal pengembalian
    $db->query("UPDATE borrow SET status='kembali', tanggal_dikembalikan=CURDATE() WHERE id=$borrow_id");
    // Ambil book_id lalu update stok buku
    $book = $db->query("SELECT book_id FROM borrow WHERE id=$borrow_id")->fetch_assoc();
    $db->query("UPDATE books SET stock = stock + 1 WHERE id=".$book['book_id']);
    echo "<div class='alert alert-success mt-3'>Buku berhasil dikembalikan!</div>";
}

// Ambil data riwayat pinjaman user
$riwayat = $db->query(
    "SELECT br.*, b.title 
     FROM borrow br
     JOIN books b ON br.book_id = b.id
     WHERE br.user_id = $user_id
     ORDER BY br.id DESC"
);
?>

<link rel="stylesheet" href="assets/bootstrap.min.css">
<div class="container mt-5">
  <h2>Riwayat Pinjaman Saya</h2>
  <table class="table table-bordered table-striped">
    <thead class="table-primary">
      <tr>
        <th>No</th>
        <th>Judul Buku</th>
        <th>Tgl Pinjam</th>
        <th>Tgl Maksimal Kembali</th>
        <th>Status</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php $no=1; while($row = $riwayat->fetch_assoc()) { ?>
        <tr>
          <td><?= $no++ ?></td>
          <td><?= htmlentities($row['title']) ?></td>
          <td><?= $row['tanggal_pinjam'] ?></td>
          <td><?= $row['tanggal_kembali'] ?></td>
          <td><?= $row['status'] ?></td>
          <td>
            <?php if($row['status'] == 'dipinjam') { ?>
              <form method="post">
                <input type="hidden" name="borrow_id" value="<?= $row['id'] ?>">
                <button class="btn btn-sm btn-success" name="kembalikan" onclick="return confirm('Kembalikan buku ini?')">Kembalikan</button>
              </form>
            <?php } else { ?>
              <span class="text-secondary">Sudah dikembalikan</span>
            <?php } ?>
          </td>
        </tr>
      <?php } ?>
    </tbody>
  </table>
  <a href="dashboard.php" class="btn btn-secondary mt-3">Kembali</a>
</div>