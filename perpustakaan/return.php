<?php session_start(); require_once 'classes/Transaction.php';
if (!isset($_SESSION['user']) || $_SESSION['user']['role']!='user' || !isset($_GET['id'])) header("Location: dashboard.php");
$t = new Transaction();
$id = intval($_GET['id']);
$fine = $t->returnBook($id);
echo "<link rel='stylesheet' href='assets/bootstrap.min.css'/>";
echo "<div class='container mt-5'><h3>Buku telah dikembalikan";
if ($fine > 0) echo ", Denda: <span class='badge bg-danger'>Rp $fine</span>";
echo "</h3><a href='dashboard.php'>Kembali</a></div>";
?>