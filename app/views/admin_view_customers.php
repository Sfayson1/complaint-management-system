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
$customers = $adminModel->getAllCustomers();
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Customers</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

<div class="page-wrapper">
    <div class="card">
        <div class="page-header">
            <h1>All Customers</h1>
            <a href="admin_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>

        <p class="page-subtitle">View and manage registered customer accounts.</p>

        <?php if (empty($customers)): ?>
            <p class="empty-state-text">No customers registered yet.</p>
        <?php else: ?>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>City</th>
                            <th>State</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($customers as $customer): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($customer["first_name"] . ' ' . $customer["last_name"]); ?></td>
                                <td><?php echo htmlspecialchars($customer["email"]); ?></td>
                                <td><?php echo htmlspecialchars($customer["phone"]); ?></td>
                                <td><?php echo htmlspecialchars($customer["city"]); ?></td>
                                <td><?php echo htmlspecialchars($customer["state"]); ?></td>
                                <td>
                                    <a href="admin_edit_customer.php?id=<?php echo $customer["customer_id"]; ?>" class="btn">Edit</a>
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
