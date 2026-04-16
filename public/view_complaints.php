<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "customer") {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/controllers/ComplaintController.php';
require_once __DIR__ . '/../app/models/Customer.php';

$db = new Database();
$conn = $db->connect();

$complaintController = new ComplaintController($conn);
$customerModel = new Customer($conn);

// Get customer_id from logged-in user using model
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
    <style>
table {
    border-collapse: collapse;
}

th, td {
    padding: 10px;
}
</style>
</head>
<body>

<h1>My Complaints</h1>

<?php if (empty($complaints)): ?>
    <p>No complaints found.</p>
<?php else: ?>
    <table border="1" cellpadding="8">
        <tr>
            <th>ID</th>
            <th>Product/Service</th>
            <th>Category</th>
            <th>Description</th>
            <th>Status</th>
        </tr>
        <?php foreach ($complaints as $complaint): ?>
            <tr>
                <td><?php echo htmlspecialchars($complaint["complaint_id"]); ?></td>
                <td><?php echo htmlspecialchars($complaint["product_name"]); ?></td>
                <td><?php echo htmlspecialchars($complaint["category_name"]); ?></td>
                <td><?php echo htmlspecialchars($complaint["description"]); ?></td>
                <td><?php echo htmlspecialchars($complaint["status"]); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>

<p><a href="customer_dashboard.php">Back to Dashboard</a></p>

</body>
</html>
