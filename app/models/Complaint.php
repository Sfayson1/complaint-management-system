<?php
class Complaint {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

public function getStatsByCustomerId($customerId) {
    $stmt = $this->conn->prepare("
        SELECT
            COUNT(*) AS total,
            SUM(status = 'open') AS open_count,
            SUM(status = 'resolved') AS resolved_count
        FROM complaints
        WHERE customer_id = ?
    ");

    $stmt->execute([$customerId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

public function create($customerId, $productServiceId, $categoryId, $description) {
    $stmt = $this->conn->prepare("
        INSERT INTO complaints (customer_id, product_service_id, category_id, description, status)
        VALUES (?, ?, ?, ?, 'open')
    ");

    $success = $stmt->execute([$customerId, $productServiceId, $categoryId, $description]);

    if ($success) {
        return $this->conn->lastInsertId();
    }

    return false;
}

public function saveImage($complaintId, $filePath) {
    $stmt = $this->conn->prepare("
        INSERT INTO complaint_images (complaint_id, file_path)
        VALUES (?, ?)
    ");

    return $stmt->execute([$complaintId, $filePath]);
}

public function getByCustomerId($customerId) {
    $stmt = $this->conn->prepare("
        SELECT
            complaints.complaint_id,
            complaints.description,
            complaints.status,
            complaints.created_at,
            products_services.name AS product_name,
            complaint_categories.name AS category_name
        FROM complaints
        INNER JOIN products_services
            ON complaints.product_service_id = products_services.product_service_id
        INNER JOIN complaint_categories
            ON complaints.category_id = complaint_categories.category_id
        WHERE complaints.customer_id = ?
        ORDER BY complaints.created_at DESC
    ");

    $stmt->execute([$customerId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function getByIdForCustomer($complaintId, $customerId) {
    $stmt = $this->conn->prepare("
        SELECT
            complaints.complaint_id,
            complaints.description,
            complaints.status,
            complaints.created_at,
            complaints.resolution_date,
            complaints.resolution_notes,
            products_services.name AS product_name,
            complaint_categories.name AS category_name
        FROM complaints
        INNER JOIN products_services
            ON complaints.product_service_id = products_services.product_service_id
        INNER JOIN complaint_categories
            ON complaints.category_id = complaint_categories.category_id
        WHERE complaints.complaint_id = ?
          AND complaints.customer_id = ?
        LIMIT 1
    ");

    $stmt->execute([$complaintId, $customerId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

public function getTechnicianNotesByComplaintId($complaintId) {
    $stmt = $this->conn->prepare("
        SELECT
            technician_notes.note,
            technician_notes.created_at,
            employees.first_name,
            employees.last_name
        FROM technician_notes
        INNER JOIN employees
            ON technician_notes.employee_id = employees.employee_id
        WHERE technician_notes.complaint_id = ?
        ORDER BY technician_notes.note_id DESC
    ");

    $stmt->execute([$complaintId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
}
