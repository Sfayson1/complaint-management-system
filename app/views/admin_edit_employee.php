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

$employeeId = $_GET["id"] ?? null;

if (!$employeeId || !is_numeric($employeeId)) {
    die("Invalid employee.");
}

$employee = $adminModel->getEmployeeById($employeeId);

if (!$employee) {
    die("Employee not found.");
}

$message = "";
$messageClass = "";

$firstName = $employee["first_name"] ?? "";
$lastName = $employee["last_name"] ?? "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $firstName = trim($_POST["first_name"] ?? "");
    $lastName = trim($_POST["last_name"] ?? "");

    if (empty($firstName) || empty($lastName)) {
        $message = "First and last name are required.";
        $messageClass = "error-message";
    } elseif (strlen($firstName) > 50 || strlen($lastName) > 50) {
        $message = "First and last name must be 50 characters or fewer.";
        $messageClass = "error-message";
    } else {
        $success = $adminModel->updateEmployee($employeeId, $firstName, $lastName);

        if ($success) {
            $message = "Employee updated successfully.";
            $messageClass = "success-message";
            $employee = $adminModel->getEmployeeById($employeeId);
        } else {
            $message = "Failed to update employee.";
            $messageClass = "error-message";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Employee</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

<div class="page-wrapper">
    <div class="card">
        <div class="page-header">
            <h1>Edit Employee</h1>
            <a href="admin_view_employees.php" class="btn btn-secondary">Back to Employees</a>
        </div>

        <p class="page-subtitle">
            Editing: <strong><?php echo htmlspecialchars($employee["first_name"] . ' ' . $employee["last_name"]); ?></strong>
            (<?php echo htmlspecialchars(ucfirst($employee["role"])); ?> &mdash; <?php echo htmlspecialchars($employee["email"]); ?>)
        </p>

        <?php if (!empty($message)): ?>
            <div class="<?php echo $messageClass; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="first_name">First Name</label>
            <input id="first_name" type="text" name="first_name" maxlength="50" value="<?php echo htmlspecialchars($firstName); ?>" required>

            <label for="last_name">Last Name</label>
            <input id="last_name" type="text" name="last_name" maxlength="50" value="<?php echo htmlspecialchars($lastName); ?>" required>

            <button type="submit">Save Changes</button>
        </form>
    </div>
</div>

</body>
</html>
