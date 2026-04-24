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
$admin = $adminModel->getEmployeeByUserId($_SESSION["user_id"]);

if (!$admin) {
    die("Admin record not found.");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

<div class="page-wrapper">
    <div class="card">
        <div class="page-header">
            <h1>Welcome, <?php echo htmlspecialchars($admin["first_name"]); ?> 👋</h1>
            <a href="logout.php" class="btn btn-secondary">Logout</a>
        </div>

        <p class="page-subtitle">Administrator dashboard</p>

        <div class="dashboard-actions">
            <a href="admin_assign_complaints.php" class="btn">Assign Complaints</a>
        </div>
    </div>
</div>

</body>
</html>
