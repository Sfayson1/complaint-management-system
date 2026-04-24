<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "customer") {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../controllers/ComplaintController.php';
require_once __DIR__ . '/../models/Customer.php';

$db = new Database();
$conn = $db->connect();

$complaintController = new ComplaintController($conn);
$customerModel = new Customer($conn);

$customer = $customerModel->getByUserId($_SESSION["user_id"]);

if (!$customer) {
    die("Customer record not found.");
}

$customerId = $customer["customer_id"];
$complaints = $complaintController->getCustomerComplaints($customerId);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Complaints</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

<div class="page-wrapper">
    <div class="card">

        <div class="page-header">
            <h1>My Complaints</h1>
            <a href="customer_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>

        <?php if (empty($complaints)): ?>
            <p class="empty-state-text">You haven't submitted any complaints yet.</p>
            <a href="submit_complaint.php" class="btn">Submit your first complaint</a>
        <?php else: ?>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Product/Service</th>
                            <th>Category</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Date Submitted</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($complaints as $complaint): ?>
                            <tr>
                                <td>#<?php echo htmlspecialchars($complaint["complaint_id"]); ?></td>
                                <td><?php echo htmlspecialchars($complaint["product_name"]); ?></td>
                                <td><?php echo htmlspecialchars($complaint["category_name"]); ?></td>
                                <td><?php echo htmlspecialchars($complaint["description"]); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo htmlspecialchars(strtolower($complaint["status"])); ?>">
                                        <?php echo htmlspecialchars($complaint["status"]); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php echo !empty($complaint["created_at"])
                                        ? htmlspecialchars(date("M j, Y", strtotime($complaint["created_at"])))
                                        : "N/A"; ?>
                                </td>
                                <td>
                                    <a href="customer_complaint_detail.php?id=<?php echo htmlspecialchars($complaint["complaint_id"]); ?>" class="btn">
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
