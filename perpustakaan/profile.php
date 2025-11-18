<?php
session_start();
require_once 'db.php';

$user_id = $_SESSION['user']['id'] ?? 1;
$db = (new Database())->conn;

// Ambil data user
$user = $db->query("SELECT * FROM user WHERE id = $user_id")->fetch_assoc();
?>

<link rel="stylesheet" href="assets/bootstrap.min.css">
<div class="container mt-5">
<h2>Profil Saya</h2>
<table class="table table-bordered w-50">
    <tr><th>Nama</th><td><?= htmlentities($user['name']) ?></td></tr>
    <tr><th>Username</th><td><?= htmlentities($user['username']) ?></td></tr>
    <tr><th>Role</th><td><?= htmlentities($user['role']) ?></td></tr>
</table>
<a href="dashboard.php" class="btn btn-secondary mt-3">Kembali</a>
</div>