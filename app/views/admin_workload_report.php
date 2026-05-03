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
$workload = $adminModel->getTechnicianWorkload();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Technician Workload Report</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

<div class="page-wrapper">
    <div class="card">
        <div class="page-header">
            <h1>Technician Workload Report</h1>
            <a href="admin_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>

        <p class="page-subtitle">Overview of complaint assignments per technician.</p>

        <?php if (empty($workload)): ?>
            <p class="empty-state-text">No technicians found.</p>
        <?php else: ?>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Technician</th>
                            <th>Open</th>
                            <th>Resolved</th>
                            <th>Total Assigned</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($workload as $row): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row["first_name"] . ' ' . $row["last_name"]); ?></td>
                                <td><?php echo (int)$row["open_count"]; ?></td>
                                <td><?php echo (int)$row["resolved_count"]; ?></td>
                                <td><?php echo (int)$row["total_assigned"]; ?></td>
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
