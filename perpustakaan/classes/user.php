<?php
require_once __DIR__ . '/../db.php';

class User {
    private $db;
    public function __construct() {
        $this->db = (new Database())->conn;
    }
    public function register($username, $password, $name) {
        $pass = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->db->prepare("INSERT INTO users (username, password, name) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $pass, $name);
        return $stmt->execute();
    }
    public function login($username, $password) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username=?");
        $stmt->bind_param("s", $username);
        $stmt->execute(); $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            if (password_verify($password, $row['password'])) {
                return $row;
            }
        }
        return false;
    }
}
?>