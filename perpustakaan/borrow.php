<?php
session_start();
require_once 'db.php';

// Asumsikan user sudah login, simpan id pengguna di $_SESSION['user']['id']
$user_id = $_SESSION['user']['id'] ?? 1; // default 1 jika belum login

$db = (new Database())->conn;

// Ambil data buku yang tersedia untuk dipinjam
$buku = $db->query("SELECT * FROM books WHERE stock > 0");

// Proses peminjaman
if(isset($_POST['pinjam'])){
    $book_id = $_POST['book_id'];
    $tanggal_pinjam = date('Y-m-d');
    $tanggal_kembali = date('Y-m-d', strtotime('+3 days'));
    $status = 'dipinjam';

    // Masukkan data peminjaman
    $stmt = $db->prepare("INSERT INTO borrow (user_id, book_id, tanggal_pinjam, tanggal_kembali, status) VALUES (?,?,?,?,?)");
    $stmt->bind_param("iisss", $user_id, $book_id, $tanggal_pinjam, $tanggal_kembali, $status);
    $stmt->execute();

    // Update stok buku
    $db->query("UPDATE books SET stock = stock - 1 WHERE id = $book_id");
    echo "<div class='alert alert-success mt-2'>Berhasil Meminjam Buku!</div>";
}

// Ambil daftar pinjaman siswa
$pinjaman = $db->query("SELECT b.title, br.tanggal_pinjam, br.tanggal_kembali, br.status 
                        FROM borrow br 
                        JOIN books b ON br.book_id = b.id 
                        WHERE br.user_id = $user_id ORDER BY br.id DESC");
?>

<link rel="stylesheet" href="assets/bootstrap.min.css">
<div class="container mt-5">
    <h2>Peminjaman Buku</h2>
    <div class="card mb-4">
      <div class="card-header bg-primary text-white">Form Peminjaman</div>
      <div class="card-body">
        <form method="post" class="row g-3 align-items-end">
          <div class="col-md-5">
            <label class="form-label">Pilih Buku</label>
            <select name="book_id" class="form-select" required>
              <option value="">- Pilih Buku -</option>
              <?php while ($row = $buku->fetch_assoc()) { ?>
                <option value="<?= $row['id'] ?>"><?= $row['title'] ?> (Stok: <?= $row['stock'] ?>)</option>
              <?php } ?>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label">Tanggal Pinjam (Hari Ini)</label>
            <input type="text" readonly value="<?= date('Y-m-d') ?>" class="form-control">
          </div>
          <div class="col-md-2">
            <label class="form-label">Tanggal Maksimal Kembali</label>
            <input type="text" readonly value="<?= date('Y-m-d', strtotime('+3 days')) ?>" class="form-control" style="color:green;font-weight:bold;">
          </div>
          <div class="col-md-1">
            <button name="pinjam" class="btn btn-success w-100">Pinjam</button>
          </div>
        </form>
      </div>
    </div>

    <h4>Daftar Pinjaman Saya</h4>
    <table class="table table-bordered table-striped">
      <thead class="table-primary">
      <tr>
        <th>No</th>
        <th>Judul Buku</th>
        <th>Tgl Pinjam</th>
        <th>Tgl Maksimal Kembali</th>
        <th>Status</th>
      </tr>
      </thead>
      <tbody>
      <?php $no=1; while($row = $pinjaman->fetch_assoc()) { ?>
        <tr>
          <td><?= $no++ ?></td>
          <td><?= htmlentities($row['title']) ?></td>
          <td><?= $row['tanggal_pinjam'] ?></td>
          <td style="color:green;"><?= $row['tanggal_kembali'] ?></td>
          <td><?= $row['status'] ?></td>
        </tr>
      <?php } ?>
      </tbody>
    </table>
    <a href="dashboard.php" class="btn btn-secondary mt-3">Kembali</a>
</div>