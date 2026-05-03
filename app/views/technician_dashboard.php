<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "technician") {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/Technician.php';

$db = new Database();
$conn = $db->connect();

$technicianModel = new Technician($conn);
$employee = $technicianModel->getEmployeeByUserId($_SESSION["user_id"]);

if (!$employee) {
    die("Technician record not found.");
}

$employeeId = $employee["employee_id"];
$assignedComplaints = $technicianModel->getAssignedComplaints($employeeId);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Technician Dashboard</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

<div class="page-wrapper">
    <div class="card">
        <div class="page-header">
            <h1>Welcome, <?php echo htmlspecialchars($employee["first_name"]); ?> 👋</h1>
            <a href="logout.php" class="btn btn-secondary">Logout</a>
        </div>

        <p class="page-subtitle">Assigned complaints dashboard</p>

        <div class="dashboard-actions">
            <a href="change_password.php" class="btn">Change Password</a>
        </div>

        <?php if (empty($assignedComplaints)): ?>
            <p class="empty-state-text">No complaints are currently assigned to you.</p>
        <?php else: ?>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Customer</th>
                            <th>Product/Service</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Date Submitted</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($assignedComplaints as $complaint): ?>
                            <tr>
                                <td>#<?php echo htmlspecialchars($complaint["complaint_id"]); ?></td>
                                <td><?php echo htmlspecialchars($complaint["first_name"] . ' ' . $complaint["last_name"]); ?></td>
                                <td><?php echo htmlspecialchars($complaint["product_name"]); ?></td>
                                <td><?php echo htmlspecialchars($complaint["category_name"]); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo htmlspecialchars(strtolower($complaint["status"])); ?>">
                                        <?php echo htmlspecialchars($complaint["status"]); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars(date("M j, Y", strtotime($complaint["created_at"]))); ?></td>
                                <td>
                                    <a href="technician_complaint_detail.php?id=<?php echo $complaint["complaint_id"]; ?>" class="btn">
                                        View
                                    </a>
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
