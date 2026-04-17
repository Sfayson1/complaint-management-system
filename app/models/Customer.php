<?php

class Customer {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getByUserId($userId) {
        $stmt = $this->conn->prepare("
            SELECT * FROM customers WHERE user_id = ?
        ");

        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateByUserId($userId, $firstName, $lastName, $streetAddress, $city, $state, $zipCode, $phone) {
        $stmt = $this->conn->prepare("
            UPDATE customers
            SET
                first_name = ?,
                last_name = ?,
                street_address = ?,
                city = ?,
                state = ?,
                zip_code = ?,
                phone = ?
            WHERE user_id = ?
        ");

        return $stmt->execute([
            $firstName,
            $lastName,
            $streetAddress,
            $city,
            $state,
            $zipCode,
            $phone,
            $userId
        ]);
    }
}
