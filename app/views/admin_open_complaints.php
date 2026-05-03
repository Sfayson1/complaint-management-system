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
$complaints = $adminModel->getOpenComplaints();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Open Complaints</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

<div class="page-wrapper">
    <div class="card">
        <div class="page-header">
            <h1>Open Complaints</h1>
            <a href="admin_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>

        <p class="page-subtitle">All open complaints and their current assignment status.</p>

        <?php if (empty($complaints)): ?>
            <p class="empty-state-text">No open complaints found.</p>
        <?php else: ?>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Customer</th>
                            <th>Product / Category</th>
                            <th>Date Submitted</th>
                            <th>Assigned To</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($complaints as $complaint): ?>
                            <tr>
                                <td>#<?php echo htmlspecialchars($complaint["complaint_id"]); ?></td>
                                <td><?php echo htmlspecialchars($complaint["first_name"] . ' ' . $complaint["last_name"]); ?></td>
                                <td>
                                    <?php echo htmlspecialchars($complaint["product_name"]); ?><br>
                                    <small style="color:#6b7280;"><?php echo htmlspecialchars($complaint["category_name"]); ?></small>
                                </td>
                                <td><?php echo htmlspecialchars(date("M j, Y", strtotime($complaint["created_at"]))); ?></td>
                                <td>
                                    <?php echo !empty($complaint["technician_first_name"])
                                        ? htmlspecialchars($complaint["technician_first_name"] . ' ' . $complaint["technician_last_name"])
                                        : "<span style='color:#ef4444;'>Unassigned</span>"; ?>
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
