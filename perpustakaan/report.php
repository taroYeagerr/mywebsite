<?php
session_start();
require_once 'db.php';

$user_id = $_SESSION['user']['id'] ?? 1;

$db = (new Database())->conn;
$notif = '';

// Fungsi hitung denda
function hitungDenda($tgl_kembali, $tgl_dikembalikan, $tarif=1000) {
    if (!$tgl_dikembalikan) return 0; // Belum dikembalikan
    $selisih = (strtotime($tgl_dikembalikan) - strtotime($tgl_kembali))/86400;
    return $selisih > 0 ? $selisih * $tarif : 0;
}

// Proses Pengembalian Buku
if (isset($_POST['kembalikan'])) {
    $borrow_id = $_POST['borrow_id'];

    // Ambil tanggal_kembali
    $pinjam = $db->query("SELECT tanggal_kembali FROM borrow WHERE id=$borrow_id")->fetch_assoc();
    $tgl_kembali = $pinjam['tanggal_kembali'];
    $tgl_dikembalikan = date('Y-m-d');

    // Hitung denda
    $selisih = (strtotime($tgl_dikembalikan) - strtotime($tgl_kembali))/86400;
    $denda = $selisih > 0 ? $selisih * 1000 : 0;

    // Update data borrow
    $db->query("UPDATE borrow SET status='kembali', tanggal_dikembalikan='$tgl_dikembalikan', denda=$denda WHERE id=$borrow_id");

    // Tambah stok buku
    $book = $db->query("SELECT book_id FROM borrow WHERE id=$borrow_id")->fetch_assoc();
    $db->query("UPDATE books SET stock = stock + 1 WHERE id=".$book['book_id']);

    // Notif
    if ($denda > 0) {
        $notif = "<div class='alert alert-warning mt-3'>Buku dikembalikan terlambat! Denda Rp " . number_format($denda, 0, ',', '.') . "</div>";
    } else {
        $notif = "<div class='alert alert-success mt-3'>Buku berhasil dikembalikan tanpa denda.</div>";
    }
}

// Ambil Riwayat Pinjaman
$riwayat = $db->query(
    "SELECT br.*, b.title 
     FROM borrow br
     JOIN books b ON br.book_id = b.id
     WHERE br.user_id = $user_id
     ORDER BY br.id DESC"
);

// Hitung total denda user
$total_denda = $db->query("SELECT SUM(denda) as total FROM borrow WHERE user_id=$user_id")->fetch_assoc()['total'] ?? 0;
?>

<link rel="stylesheet" href="assets/bootstrap.min.css">
<div class="container mt-5">
  <h2>Riwayat Pinjaman Saya</h2>
  <?= $notif ?>
  <div class="mb-3">
    <strong>Total Denda Anda: </strong>
    <span style="color:red;font-weight:bold;">
      <?= $total_denda ? 'Rp '.number_format($total_denda,0,',','.') : 'Tidak ada denda.' ?>
    </span>
  </div>
  <table class="table table-bordered table-striped">
    <thead class="table-primary">
      <tr>
        <th>No</th>
        <th>Judul Buku</th>
        <th>Tgl Pinjam</th>
        <th>Tgl Maksimal Kembali</th>
        <th>Tgl Dikembalikan</th>
        <th>Status</th>
        <th>Denda</th>
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
          <td><?= $row['tanggal_dikembalikan'] ?: '-' ?></td>
          <td><?= $row['status'] ?></td>
          <td>
            <?= ($row['denda']) ? 'Rp '.number_format($row['denda'],0,',','.') : '-' ?>
          </td>
          <td>
            <?php if($row['status'] == 'dipinjam') { ?>
              <form method="post">
                <input type="hidden" name="borrow_id" value="<?= $row['id'] ?>">
                <button class="btn btn-sm btn-success" name="kembalikan"
                        onclick="return confirm('Kembalikan buku ini? Jika terlambat ada denda!')">Kembalikan</button>
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