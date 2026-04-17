<?php

class Admin {
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

    public function getAllTechnicians() {
        $stmt = $this->conn->prepare("
            SELECT employees.employee_id, employees.first_name, employees.last_name
            FROM employees
            INNER JOIN users ON employees.user_id = users.user_id
            WHERE users.role = 'technician'
            ORDER BY employees.last_name, employees.first_name
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOpenComplaints() {
        $stmt = $this->conn->prepare("
            SELECT
                complaints.complaint_id,
                complaints.status,
                complaints.created_at,
                customers.first_name,
                customers.last_name,
                products_services.name AS product_name,
                complaint_categories.name AS category_name,
                assigned_emp.first_name AS technician_first_name,
                assigned_emp.last_name AS technician_last_name,
                complaint_assignments.employee_id
            FROM complaints
            INNER JOIN customers
                ON complaints.customer_id = customers.customer_id
            INNER JOIN products_services
                ON complaints.product_service_id = products_services.product_service_id
            INNER JOIN complaint_categories
                ON complaints.category_id = complaint_categories.category_id
            LEFT JOIN complaint_assignments
                ON complaints.complaint_id = complaint_assignments.complaint_id
            LEFT JOIN employees AS assigned_emp
                ON complaint_assignments.employee_id = assigned_emp.employee_id
            WHERE complaints.status = 'open'
            ORDER BY complaints.complaint_id DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function assignComplaint($complaintId, $employeeId) {
        // Check if assignment already exists
        $checkStmt = $this->conn->prepare("
            SELECT assignment_id FROM complaint_assignments WHERE complaint_id = ?
        ");
        $checkStmt->execute([$complaintId]);
        $existing = $checkStmt->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            $stmt = $this->conn->prepare("
                UPDATE complaint_assignments
                SET employee_id = ?
                WHERE complaint_id = ?
            ");
            return $stmt->execute([$employeeId, $complaintId]);
        } else {
            $stmt = $this->conn->prepare("
                INSERT INTO complaint_assignments (complaint_id, employee_id)
                VALUES (?, ?)
            ");
            return $stmt->execute([$complaintId, $employeeId]);
        }
    }
}
