<?php
require_once __DIR__ . '/../db.php';

class Transaction {
    private $db;
    public function __construct() {
        $this->db = (new Database())->conn;
    }
    public function borrow($user_id, $book_id) {
        // Maks 3 buku sedang dipinjam
        $res = $this->db->query("SELECT COUNT(*) as cnt FROM transactions WHERE user_id = $user_id AND status='borrowed'");
        $row = $res->fetch_assoc();
        if ($row['cnt'] >= 3) return false;
        // Cek stok
        $rstok = $this->db->query("SELECT stock FROM books WHERE id = $book_id");
        $st = $rstok->fetch_assoc();
        if ($st['stock'] <= 0) return false;
        // Pinjam
        $today = date('Y-m-d');
        $stmt = $this->db->prepare("INSERT INTO transactions (user_id, book_id, borrow_date) VALUES (?,?,?)");
        $stmt->bind_param("iis", $user_id, $book_id, $today);
        $rs = $stmt->execute();
        // Kurangi stok
        $this->db->query("UPDATE books SET stock=stock-1 WHERE id=$book_id");
        return $rs;
    }
    public function returnBook($id) {
        // Get transaction info
        $tr = $this->db->query("SELECT * FROM transactions WHERE id=$id")->fetch_assoc();
        $book_id = $tr['book_id'];
        $borrow_date = $tr['borrow_date'];
        $return_date = date('Y-m-d');
        // Hitung denda: >7 hari denda 1000/hari
        $days = (strtotime($return_date)-strtotime($borrow_date))/(60*60*24);
        $fine = $days > 7 ? (($days-7)*1000) : 0;
        $this->db->query("UPDATE transactions SET return_date='$return_date', status='returned', fine=$fine WHERE id=$id");
        // Tambah stok buku lagi
        $this->db->query("UPDATE books SET stock=stock+1 WHERE id=$book_id");
        if ($fine > 0) {
            $this->db->query("INSERT INTO fines (transaction_id, amount) VALUES ($id, $fine)");
        }
        return $fine;
    }
    public function getBorrowed($user_id) {
        return $this->db->query("SELECT t.*,b.title FROM transactions t JOIN books b ON t.book_id=b.id WHERE t.user_id=$user_id AND t.status='borrowed'");
    }
    public function getFines($user_id) {
        if ($user_id == 0) // admin: ambil semua belum dibayar
            return $this->db->query("SELECT f.*, b.title, u.name FROM fines f JOIN transactions t ON f.transaction_id=t.id JOIN books b ON t.book_id=b.id JOIN users u ON t.user_id=u.id WHERE f.paid=0");
        else // user: hanya milik sendiri
            return $this->db->query("SELECT f.*, b.title FROM fines f JOIN transactions t ON f.transaction_id=t.id JOIN books b ON t.book_id=b.id WHERE f.paid=0 AND t.user_id=$user_id");
    }
    public function payFine($fine_id) {
        return $this->db->query("UPDATE fines SET paid=1 WHERE id=$fine_id");
    }
}
?>