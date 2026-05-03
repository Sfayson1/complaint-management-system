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

$message = "";
$messageClass = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $complaintId = $_POST["complaint_id"] ?? "";
    $employeeId = $_POST["employee_id"] ?? "";

    if (empty($complaintId) || empty($employeeId)) {
        $message = "Please select a technician.";
        $messageClass = "error-message";
    } elseif (!is_numeric($complaintId) || !is_numeric($employeeId)) {
        $message = "Invalid complaint or technician selection.";
        $messageClass = "error-message";
    } else {
        $success = $adminModel->assignComplaint($complaintId, $employeeId);

        if ($success) {
            $message = "Complaint assigned successfully.";
            $messageClass = "success-message";
        } else {
            $message = "Failed to assign complaint.";
            $messageClass = "error-message";
        }
    }
}

$complaints = $adminModel->getUnassignedComplaints();
$technicians = $adminModel->getAllTechnicians();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Assign Complaints</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

<div class="page-wrapper">
    <div class="card">
        <div class="page-header">
            <h1>Assign Complaints</h1>
            <a href="admin_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>

        <p class="page-subtitle">Assign unassigned complaints to technicians.</p>

        <?php if (!empty($message)): ?>
            <div class="<?php echo $messageClass; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <?php if (empty($complaints)): ?>
            <p class="empty-state-text">All open complaints are currently assigned.</p>
        <?php else: ?>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Customer</th>
                            <th>Product / Category</th>
                            <th>Date Submitted</th>
                            <th>Assign To</th>
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
                                <td class="assign-cell">
                                    <form method="POST" action="">
                                        <input type="hidden" name="complaint_id" value="<?php echo htmlspecialchars($complaint["complaint_id"]); ?>">
                                        <div class="assign-controls">
                                            <select name="employee_id" required>
                                                <option value="">Select Technician</option>
                                                <?php foreach ($technicians as $tech): ?>
                                                    <option value="<?php echo htmlspecialchars($tech["employee_id"]); ?>">
                                                        <?php echo htmlspecialchars($tech["first_name"] . ' ' . $tech["last_name"]); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <button type="submit">Assign</button>
                                        </div>
                                    </form>
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
