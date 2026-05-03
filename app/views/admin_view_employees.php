<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "administrator") {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/Admin.php';

$db = new Database();
$conn = $db->connect();

$adminModel = new Admin($conn);
$employees = $adminModel->getAllEmployees();
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Employees</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

<div class="page-wrapper">
    <div class="card">
        <div class="page-header">
            <h1>All Employees</h1>
            <a href="admin_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>

        <p class="page-subtitle">View and manage technician and administrator accounts.</p>

        <div style="margin-bottom: 16px;">
            <a href="admin_add_employee.php" class="btn">Add Employee</a>
        </div>

        <?php if (empty($employees)): ?>
            <p class="empty-state-text">No employees found.</p>
        <?php else: ?>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($employees as $employee): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($employee["first_name"] . ' ' . $employee["last_name"]); ?></td>
                                <td><?php echo htmlspecialchars($employee["email"]); ?></td>
                                <td><?php echo htmlspecialchars(ucfirst($employee["role"])); ?></td>
                                <td>
                                    <a href="admin_edit_employee.php?id=<?php echo $employee["employee_id"]; ?>" class="btn">Edit</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

    </div>
</div>

</body>
</html>
