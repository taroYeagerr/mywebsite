<?php
session_start();
require_once 'db.php';

$db = (new Database())->conn;
$error = ''; $success = '';

if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $name     = $_POST['name'];

    // Cek user sudah ada
    $cek = $db->query("SELECT id FROM user WHERE username='$username'");
    if ($cek->num_rows > 0) {
        $error = "Username sudah dipakai!";
    } else {
        $stmt = $db->prepare("INSERT INTO user (username, password, name, role) VALUES (?,?,?,?)");
        $role = "siswa";
        $stmt->bind_param("ssss", $username, $password, $name, $role);
        $stmt->execute();
        $success = "Berhasil daftar! Silakan login";
        // Atau langsung login: $_SESSION['user'] = array_merge($_POST,['role'=>$role]);
        // header("Location: dashboard.php");
    }
}
?>

<!DOCTYPE html>
<html>
<head><title>Register</title><link rel="stylesheet" href="assets/bootstrap.min.css"></head>
<body>
<div class="container mt-5">
    <h2>Registrasi Siswa</h2>
    <?php if($error) echo "<div class='alert alert-danger'>$error</div>"; ?>
    <?php if($success) echo "<div class='alert alert-success'>$success</div>"; ?>
    <form method="post">
        <input type="text" name="name" class="form-control mb-3" placeholder="Nama Lengkap" required>
        <input type="text" name="username" class="form-control mb-3" placeholder="Username" required>
        <input type="password" name="password" class="form-control mb-3" placeholder="Password" required>
        <button name="register" class="btn btn-success">Register</button>
        <a href="login.php" class="btn btn-link">Login</a>
    </form>
</div>
</body>
</html>