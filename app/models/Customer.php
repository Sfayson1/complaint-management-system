<?php

class Customer {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getByUserId($userId) {
        $stmt = $this->conn->prepare("
            SELECT customer_id FROM customers WHERE user_id = ?
        ");

        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
