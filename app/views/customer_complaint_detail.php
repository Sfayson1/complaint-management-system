<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

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

if (!$customer) {
    die("Customer record not found.");
}

$customerId = $customer["customer_id"];
$complaintId = $_GET["id"] ?? null;

if (!$complaintId || !is_numeric($complaintId)) {
    die("Invalid complaint.");
}

$complaint = $complaintModel->getByIdForCustomer($complaintId, $customerId);

if (!$complaint) {
    die("Complaint not found.");
}

$notes = $complaintModel->getTechnicianNotesByComplaintId($complaintId);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Complaint Detail</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

<div class="admin-page-wrapper">
    <div class="card">
        <div class="page-header">
            <h1>Complaint #<?php echo htmlspecialchars($complaint["complaint_id"]); ?></h1>
            <a href="view_complaints.php" class="btn btn-secondary">Back to My Complaints</a>
        </div>

        <p class="page-subtitle">View complaint details, technician notes, and resolution updates.</p>

        <div class="detail-grid">
            <div class="detail-card">
                <h2>Complaint Details</h2>
                <p><strong>Product/Service:</strong> <?php echo htmlspecialchars($complaint["product_name"]); ?></p>
                <p><strong>Category:</strong> <?php echo htmlspecialchars($complaint["category_name"]); ?></p>
                <p>
                    <strong>Status:</strong>
                    <span class="status-badge status-<?php echo htmlspecialchars(strtolower($complaint["status"])); ?>">
                        <?php echo htmlspecialchars($complaint["status"]); ?>
                    </span>
                </p>
                <p><strong>Date Submitted:</strong> <?php echo htmlspecialchars(date("M j, Y", strtotime($complaint["created_at"]))); ?></p>

                <?php if (!empty($complaint["resolution_date"])): ?>
                    <p><strong>Resolved On:</strong> <?php echo htmlspecialchars(date("M j, Y", strtotime($complaint["resolution_date"]))); ?></p>
                <?php endif; ?>
            </div>

            <div class="detail-card">
                <h2>Description</h2>
                <p><?php echo htmlspecialchars($complaint["description"]); ?></p>
            </div>
        </div>

        <div class="detail-card" style="margin-top: 24px;">
            <h2>Technician Notes</h2>

            <?php if (empty($notes)): ?>
                <p class="empty-state-text">No technician notes have been added yet.</p>
            <?php else: ?>
                <?php foreach ($notes as $note): ?>
                    <div class="note-card">
                        <p><strong><?php echo htmlspecialchars($note["first_name"] . ' ' . $note["last_name"]); ?></strong></p>
                        <p><?php echo htmlspecialchars($note["note"] ?? ""); ?></p>
                        <small><?php echo htmlspecialchars(date("M j, Y g:i A", strtotime($note["created_at"]))); ?></small>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <?php if (!empty($complaint["resolution_notes"])): ?>
            <div class="detail-card" style="margin-top: 24px;">
                <h2>Resolution Notes</h2>
                <div class="resolution-notes">
                    <?php echo htmlspecialchars($complaint["resolution_notes"]); ?>
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>

</body>
</html>
