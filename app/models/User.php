<?php

class User {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function findByEmail($email) {
        $stmt = $this->conn->prepare("
            SELECT users.*, customers.first_name
            FROM users
            LEFT JOIN customers
                ON users.user_id = customers.user_id
            WHERE users.email = ?
        ");

        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findById($userId) {
        $stmt = $this->conn->prepare("
            SELECT * FROM users WHERE user_id = ?
        ");
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updatePassword($userId, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("
            UPDATE users SET password_hash = ? WHERE user_id = ?
        ");
        return $stmt->execute([$hashedPassword, $userId]);
    }

    public function create($email, $password) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->conn->prepare("
            INSERT INTO users (email, password_hash, role)
            VALUES (?, ?, 'customer')
        ");

        $success = $stmt->execute([$email, $hashedPassword]);

        if ($success) {
            return $this->conn->lastInsertId();
        }

        return false;
    }
}
