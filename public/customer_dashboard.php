<?php
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "customer") {
    header("Location: login.php");
    exit();
}
require_once __DIR__ . '/../config/database.php';

$db = new Database();
$conn = $db->connect();

// Get customer_id
$stmt = $conn->prepare("SELECT customer_id FROM customers WHERE user_id = ?");
$stmt->execute([$_SESSION["user_id"]]);
$customer = $stmt->fetch(PDO::FETCH_ASSOC);

$complaintCount = 0;
$openCount = 0;
$resolvedCount = 0;

if ($customer) {
    $countStmt = $conn->prepare("SELECT COUNT(*) FROM complaints WHERE customer_id = ?");
    $countStmt->execute([$customer["customer_id"]]);
    $complaintCount = $countStmt->fetchColumn();

    $openStmt = $conn->prepare("SELECT COUNT(*) FROM complaints WHERE customer_id = ? AND status = 'open'");
    $openStmt->execute([$customer["customer_id"]]);
    $openCount = $openStmt->fetchColumn();

    $resolvedStmt = $conn->prepare("SELECT COUNT(*) FROM complaints WHERE customer_id = ? AND status = 'resolved'");
    $resolvedStmt->execute([$customer["customer_id"]]);
    $resolvedCount = $resolvedStmt->fetchColumn();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Customer Dashboard</title>
    <link rel="stylesheet" href="assets/css/style.css">
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
        </div>

        <div class="dashboard-footer">
            <a href="logout.php" class="btn btn-secondary">Logout</a>
        </div>

    </div>
</div>

</body>
</html>
