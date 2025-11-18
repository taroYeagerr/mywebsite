<?php
require_once 'config.php';

class Database {
    public $conn;
    function __construct() {
        $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($this->conn->connect_error) {
            die("Koneksi DB error: " . $this->conn->connect_error);
        }
    }
}
?>