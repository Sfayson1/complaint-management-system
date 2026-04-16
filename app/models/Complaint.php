<?php
class Complaint {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($customerId, $productServiceId, $categoryId, $description) {
        $stmt = $this->conn->prepare("
            INSERT INTO complaints (customer_id, product_service_id, category_id, description, status)
            VALUES (?, ?, ?, ?, 'open')
        ");

        return $stmt->execute([$customerId, $productServiceId, $categoryId, $description]);
    }

    public function getByCustomerId($customerId) {
        $stmt = $this->conn->prepare("
            SELECT
                complaints.complaint_id,
                products_services.name AS product_name,
                complaint_categories.name AS category_name,
                complaints.description,
                complaints.status
            FROM complaints
            INNER JOIN products_services
                ON complaints.product_service_id = products_services.product_service_id
            INNER JOIN complaint_categories
                ON complaints.category_id = complaint_categories.category_id
            WHERE complaints.customer_id = ?
            ORDER BY complaints.complaint_id DESC
        ");

        $stmt->execute([$customerId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
