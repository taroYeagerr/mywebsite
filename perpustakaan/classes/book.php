<?php
require_once __DIR__ . '/../db.php';

class Book {
    private $db;
    public function __construct() {
        $this->db = (new Database())->conn;
    }
    public function getAll() {
        return $this->db->query("SELECT * FROM books");
    }
}
?>