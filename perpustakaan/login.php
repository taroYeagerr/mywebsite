<?php
session_start();
require_once 'db.php';

$db = (new Database())->conn;
$error = '';

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Cek user di tabel
    $result = $db->query("SELECT * FROM user WHERE username='$username'");
    if ($row = $result->fetch_assoc()) {
        if ($password == $row['password']) { // pastikan password diisi plain-text
            $_SESSION['user'] = $row;
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Username tidak ditemukan!";
    }
}
?>

<!DOCTYPE html>
<html>
<head><title>Login</title><link rel="stylesheet" href="assets/bootstrap.min.css"></head>
<body>
<div class="container mt-5">
    <h2>Login Perpustakaan</h2>
    <?php if($error) echo "<div class='alert alert-danger'>$error</div>"; ?>
    <form method="post">
        <input type="text" name="username" class="form-control mb-3" placeholder="Username" required>
        <input type="password" name="password" class="form-control mb-3" placeholder="Password" required>
        <button name="login" class="btn btn-primary">Login</button>
        <a href="register.php" class="btn btn-link">Register</a>
    </form>
</div>
</body>
</html>