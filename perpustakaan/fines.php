<?php session_start(); require_once 'classes/Transaction.php';
if (!isset($_SESSION['user'])) header("Location: login.php");
$user = $_SESSION['user'];
$t = new Transaction();
if ($_POST && $user['role']=='admin' && isset($_POST['fine_id'])) {
    $t->payFine($_POST['fine_id']);
}
$fines = $t->getFines($user['role']=='admin' ? 0 : $user['id']);
?>
<link rel="stylesheet" href="assets/bootstrap.min.css"/>
<div class="container mt-5">
<h2>Denda <?= $user['role']=='admin'?'Belum Dibayar':'Saya' ?></h2>
<table class="table">
    <tr><th>Buku</th><th>Denda</th><?php if($user['role']=='admin')echo"<th>Peminjam</th>";?><th>Aksi</th></tr>
    <?php while ($row = $fines->fetch_assoc()) { ?>
    <tr>
        <td><?= htmlentities($row['title']) ?></td>
        <td>Rp <?= $row['amount'] ?></td>
        <?php if ($user['role']=='admin') echo "<td>".htmlentities($row['name'])."</td>"; ?>
        <td>
        <?php if ($user['role']=='admin') { ?>
            <form method="post"><input type="hidden" name="fine_id" value="<?= $row['id'] ?>"/><button class="btn btn-success btn-sm">Tandai Lunas</button></form>
        <?php } else { echo "<span class='text-danger'>Bayar ke admin</span>"; } ?>
        </td>
    </tr>
    <?php } ?>
</table>
<div><a href="dashboard.php">Kembali</a></div>
</div>