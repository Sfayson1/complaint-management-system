<?php

class Technician {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getEmployeeByUserId($userId) {
        $stmt = $this->conn->prepare("
            SELECT * FROM employees WHERE user_id = ?
        ");
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

public function getAssignedComplaints($employeeId) {
    $stmt = $this->conn->prepare("
        SELECT
            complaints.complaint_id,
            complaints.description,
            complaints.status,
            complaints.created_at,
            customers.first_name,
            customers.last_name,
            products_services.name AS product_name,
            complaint_categories.name AS category_name
        FROM complaint_assignments
        INNER JOIN complaints
            ON complaint_assignments.complaint_id = complaints.complaint_id
        INNER JOIN customers
            ON complaints.customer_id = customers.customer_id
        INNER JOIN products_services
            ON complaints.product_service_id = products_services.product_service_id
        INNER JOIN complaint_categories
            ON complaints.category_id = complaint_categories.category_id
        WHERE complaint_assignments.employee_id = ?
        ORDER BY complaints.created_at DESC
    ");

    $stmt->execute([$employeeId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function getAssignedComplaintById($complaintId, $employeeId) {
    $stmt = $this->conn->prepare("
        SELECT
            complaints.complaint_id,
            complaints.description,
            complaints.status,
            complaints.created_at,
            complaints.resolution_date,
            complaints.resolution_notes,
            customers.first_name,
            customers.last_name,
            customers.street_address,
            customers.city,
            customers.state,
            customers.zip_code,
            customers.phone,
            products_services.name AS product_name,
            complaint_categories.name AS category_name,
            complaint_images.file_path
        FROM complaint_assignments
        INNER JOIN complaints
            ON complaint_assignments.complaint_id = complaints.complaint_id
        INNER JOIN customers
            ON complaints.customer_id = customers.customer_id
        INNER JOIN products_services
            ON complaints.product_service_id = products_services.product_service_id
        INNER JOIN complaint_categories
            ON complaints.category_id = complaint_categories.category_id
        LEFT JOIN complaint_images
            ON complaints.complaint_id = complaint_images.complaint_id
        WHERE complaint_assignments.employee_id = ?
          AND complaints.complaint_id = ?
        LIMIT 1
    ");

    $stmt->execute([$employeeId, $complaintId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

public function addTechnicianNote($complaintId, $employeeId, $noteText) {
    $stmt = $this->conn->prepare("
        INSERT INTO technician_notes (complaint_id, employee_id, note)
        VALUES (?, ?, ?)
    ");

    return $stmt->execute([$complaintId, $employeeId, $noteText]);
}

public function getTechnicianNotes($complaintId) {
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

public function resolveComplaint($complaintId, $resolutionNotes) {
    $stmt = $this->conn->prepare("
        UPDATE complaints
        SET
            status = 'resolved',
            resolution_date = NOW(),
            resolution_notes = ?
        WHERE complaint_id = ?
    ");

    return $stmt->execute([$resolutionNotes, $complaintId]);
}
}
