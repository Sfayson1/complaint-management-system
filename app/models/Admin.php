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

    public function getUnassignedComplaints() {
        $stmt = $this->conn->prepare("
            SELECT
                complaints.complaint_id,
                complaints.status,
                complaints.created_at,
                customers.first_name,
                customers.last_name,
                products_services.name AS product_name,
                complaint_categories.name AS category_name
            FROM complaints
            INNER JOIN customers
                ON complaints.customer_id = customers.customer_id
            INNER JOIN products_services
                ON complaints.product_service_id = products_services.product_service_id
            INNER JOIN complaint_categories
                ON complaints.category_id = complaint_categories.category_id
            LEFT JOIN complaint_assignments
                ON complaints.complaint_id = complaint_assignments.complaint_id
            WHERE complaints.status = 'open'
              AND complaint_assignments.assignment_id IS NULL
            ORDER BY complaints.created_at ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function assignComplaint($complaintId, $employeeId) {
        $checkStmt = $this->conn->prepare("
            SELECT assignment_id FROM complaint_assignments WHERE complaint_id = ?
        ");
        $checkStmt->execute([$complaintId]);
        $existing = $checkStmt->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            $stmt = $this->conn->prepare("
                UPDATE complaint_assignments SET employee_id = ? WHERE complaint_id = ?
            ");
            return $stmt->execute([$employeeId, $complaintId]);
        } else {
            $stmt = $this->conn->prepare("
                INSERT INTO complaint_assignments (complaint_id, employee_id) VALUES (?, ?)
            ");
            return $stmt->execute([$complaintId, $employeeId]);
        }
    }

    // ── Customers ──────────────────────────────────────────────

    public function getAllCustomers() {
        $stmt = $this->conn->prepare("
            SELECT customers.*, users.email
            FROM customers
            INNER JOIN users ON customers.user_id = users.user_id
            ORDER BY customers.last_name, customers.first_name
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCustomerById($customerId) {
        $stmt = $this->conn->prepare("
            SELECT customers.*, users.email
            FROM customers
            INNER JOIN users ON customers.user_id = users.user_id
            WHERE customers.customer_id = ?
        ");
        $stmt->execute([$customerId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateCustomer($customerId, $firstName, $lastName, $streetAddress, $city, $state, $zipCode, $phone) {
        $stmt = $this->conn->prepare("
            UPDATE customers
            SET first_name = ?, last_name = ?, street_address = ?,
                city = ?, state = ?, zip_code = ?, phone = ?
            WHERE customer_id = ?
        ");
        return $stmt->execute([$firstName, $lastName, $streetAddress, $city, $state, $zipCode, $phone, $customerId]);
    }

    // ── Employees ──────────────────────────────────────────────

    public function getAllEmployees() {
        $stmt = $this->conn->prepare("
            SELECT employees.*, users.email, users.role
            FROM employees
            INNER JOIN users ON employees.user_id = users.user_id
            ORDER BY employees.last_name, employees.first_name
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getEmployeeById($employeeId) {
        $stmt = $this->conn->prepare("
            SELECT employees.*, users.email, users.role
            FROM employees
            INNER JOIN users ON employees.user_id = users.user_id
            WHERE employees.employee_id = ?
        ");
        $stmt->execute([$employeeId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addEmployee($email, $password, $role, $firstName, $lastName) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $userStmt = $this->conn->prepare("
            INSERT INTO users (email, password_hash, role) VALUES (?, ?, ?)
        ");
        $success = $userStmt->execute([$email, $hashedPassword, $role]);

        if (!$success) {
            return false;
        }

        $userId = $this->conn->lastInsertId();

        $empStmt = $this->conn->prepare("
            INSERT INTO employees (user_id, first_name, last_name) VALUES (?, ?, ?)
        ");
        return $empStmt->execute([$userId, $firstName, $lastName]);
    }

    public function updateEmployee($employeeId, $firstName, $lastName) {
        $stmt = $this->conn->prepare("
            UPDATE employees SET first_name = ?, last_name = ? WHERE employee_id = ?
        ");
        return $stmt->execute([$firstName, $lastName, $employeeId]);
    }

    // ── Workload Report ────────────────────────────────────────

    public function getTechnicianWorkload() {
        $stmt = $this->conn->prepare("
            SELECT
                employees.employee_id,
                employees.first_name,
                employees.last_name,
                COUNT(complaint_assignments.complaint_id) AS total_assigned,
                SUM(complaints.status = 'open') AS open_count,
                SUM(complaints.status = 'resolved') AS resolved_count
            FROM employees
            INNER JOIN users ON employees.user_id = users.user_id
            LEFT JOIN complaint_assignments
                ON employees.employee_id = complaint_assignments.employee_id
            LEFT JOIN complaints
                ON complaint_assignments.complaint_id = complaints.complaint_id
            WHERE users.role = 'technician'
            GROUP BY employees.employee_id
            ORDER BY open_count DESC, employees.last_name ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
