<?php
session_start();
require_once 'db.php';

$db = (new Database())->conn;

// Statistik
$total_buku = $db->query("SELECT COUNT(*) as jml FROM books")->fetch_assoc()['jml'];
$total_pinjaman = $db->query("SELECT COUNT(*) as jml FROM borrow")->fetch_assoc()['jml'];
$total_user = $db->query("SELECT COUNT(*) as jml FROM user")->fetch_assoc()['jml'];

// Pilih gambar satu cover buku acak sebagai banner
$cover_q = $db->query("SELECT cover FROM books WHERE cover <> '' LIMIT 1");
$cover_src = "assets/perpustakaan.jpg";

// Usernam
$username = $_SESSION['user']['name'] ?? 'Siswa';
?>

<link rel="stylesheet" href="assets/bootstrap.min.css">

<style>
.dashboard-banner {
    width: 100%; height: 230px; object-fit: cover; border-radius: 2em;
    box-shadow: 0 4px 14px rgba(0,0,0,0.14);
    margin-bottom: 1.6em;
}
.menu-icon {
    width: 50px; height: 50px; object-fit: contain;
    margin-bottom: 12px;
}
.card-stats {
    text-align: center; border-radius:16px;
}
</style>

<div class="container mt-5 mb-4">

    <img src="<?= $cover_src ?>" alt="Banner Buku" class="dashboard-banner mb-4">
    
    <h2 class="mb-2">Selamat datang, <span class="text-primary"><?= htmlentities($username) ?></span>!</h2>
    <p class="text-secondary fs-5 mb-4">Aplikasi Perpustakaan Digital Siswa</p>
    
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card card-stats shadow-sm border-primary">
                <div class="card-body">
                    <img src="assets/covers/naruto.jpg" class="menu-icon" alt="Buku">
                    <h5>Buku</h5>
                    <div class="fs-2 text-primary"><?= $total_buku ?></div>
                    <span class="text-muted">Total Buku</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-stats shadow-sm border-success">
                <div class="card-body">
                    <img src="assets/covers/attack_on_titan.jpg" class="menu-icon" alt="Pinjaman">
                    <h5>Pinjaman</h5>
                    <div class="fs-2 text-success"><?= $total_pinjaman ?></div>
                    <span class="text-muted">Total Pinjaman</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-stats shadow-sm border-info">
                <div class="card-body">
                    <img src="https://cdn-icons-png.flaticon.com/512/847/847969.png" class="menu-icon" alt="Siswa">
                    <h5>Pengguna</h5>
                    <div class="fs-2 text-info"><?= $total_user ?></div>
                    <span class="text-muted">Total Siswa</span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-2">
      <div class="col-md-3 mb-3">
        <a href="books.php" class="card text-decoration-none text-dark shadow-sm h-100">
          <div class="card-body text-center">
            <img src="https://cdn-icons-png.flaticon.com/512/1828/1828884.png" class="menu-icon" alt="Daftar Buku">
            <div class="fs-5 fw-bold">Lihat Daftar Buku</div>
          </div>
        </a>
      </div>
      <div class="col-md-3 mb-3">
        <a href="borrow.php" class="card text-decoration-none text-dark shadow-sm h-100">
          <div class="card-body text-center">
            <img src="https://cdn-icons-png.flaticon.com/512/2553/2553695.png" class="menu-icon" alt="Pinjam Buku">
            <div class="fs-5 fw-bold">Pinjam Buku</div>
          </div>
        </a>
      </div>
      <div class="col-md-3 mb-3">
        <a href="report.php" class="card text-decoration-none text-dark shadow-sm h-100">
          <div class="card-body text-center">
            <img src="https://cdn-icons-png.flaticon.com/512/8676/8676546.png" class="menu-icon" alt="Riwayat">
            <div class="fs-5 fw-bold">Riwayat Pinjaman</div>
          </div>
        </a>
      </div>
      <div class="col-md-3 mb-3">
        <a href="profile.php" class="card text-decoration-none text-dark shadow-sm h-100">
          <div class="card-body text-center">
            <img src="https://cdn-icons-png.flaticon.com/512/1077/1077063.png" class="menu-icon" alt="Profil">
            <div class="fs-5 fw-bold">Profil Saya</div>
          </div>
        </a>
      </div>
    </div>
</div>