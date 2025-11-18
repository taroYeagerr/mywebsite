<?php
session_start();
require_once 'db.php';

$user_id = $_SESSION['user']['id'] ?? 1; // default 1 jika belum login
$db = (new Database())->conn;

// Ambil riwayat pinjaman user
$riwayat = $db->query("SELECT br.*, b.title 
    FROM borrow br
    JOIN books b ON br.book_id = b.id
    WHERE br.user_id = $user_id
    ORDER BY br.id DESC");
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
        </tr>
    <?php } ?>
    </tbody>
</table>
<a href="dashboard.php" class="btn btn-secondary mt-3">Kembali</a>
</div>