<?php
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "customer") {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/Customer.php';
require_once __DIR__ . '/../models/Complaint.php';

$db = new Database();
$conn = $db->connect();

$customerModel = new Customer($conn);
$complaintModel = new Complaint($conn);

$customer = $customerModel->getByUserId($_SESSION["user_id"]);

$complaintCount = 0;
$openCount = 0;
$resolvedCount = 0;

if ($customer) {
    $stats = $complaintModel->getStatsByCustomerId($customer["customer_id"]);
    $complaintCount = $stats["total"] ?? 0;
    $openCount = $stats["open_count"] ?? 0;
    $resolvedCount = $stats["resolved_count"] ?? 0;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Customer Dashboard</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

<div class="page-wrapper">
    <div class="card">

        <h1>Welcome, <?php echo htmlspecialchars($_SESSION["first_name"] ?? "Customer"); ?> 👋</h1>
        <p>Your complaint management dashboard</p>

        <hr class="section-divider">
        <div class="dashboard-stats">

            <div class="stat-card">
                <h2><?php echo $complaintCount; ?></h2>
                <p>Total Complaints</p>
            </div>

            <div class="stat-card">
                <h2><?php echo $openCount; ?></h2>
                <p>Open</p>
            </div>

            <div class="stat-card">
                <h2><?php echo $resolvedCount; ?></h2>
                <p>Resolved</p>
            </div>

        </div>

        <div class="dashboard-actions">
            <a href="submit_complaint.php" class="btn">Submit a Complaint</a>
            <a href="view_complaints.php" class="btn">View My Complaints</a>
            <a href="update_profile.php" class="btn">Update My Profile</a>
            <a href="change_password.php" class="btn">Change Password</a>
        </div>

        <div class="dashboard-footer">
            <a href="logout.php" class="btn btn-secondary">Logout</a>
        </div>

    </div>
</div>

</body>
</html>
