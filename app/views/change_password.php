<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/User.php';

$db = new Database();
$conn = $db->connect();

$userModel = new User($conn);

$message = "";
$messageClass = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $currentPassword = $_POST["current_password"] ?? "";
    $newPassword = $_POST["new_password"] ?? "";
    $confirmPassword = $_POST["confirm_password"] ?? "";

    $user = $userModel->findById($_SESSION["user_id"]);

    if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        $message = "All fields are required.";
        $messageClass = "error-message";
    } elseif (!password_verify($currentPassword, $user["password_hash"])) {
        $message = "Current password is incorrect.";
        $messageClass = "error-message";
    } elseif ($newPassword !== $confirmPassword) {
        $message = "New passwords do not match.";
        $messageClass = "error-message";
    } elseif (
        strlen($newPassword) < 8 ||
        !preg_match('/[A-Z]/', $newPassword) ||
        !preg_match('/[a-z]/', $newPassword) ||
        !preg_match('/[0-9]/', $newPassword)
    ) {
        $message = "New password must be at least 8 characters and include uppercase, lowercase, and a number.";
        $messageClass = "error-message";
    } elseif ($newPassword === $currentPassword) {
        $message = "New password must be different from your current password.";
        $messageClass = "error-message";
    } else {
        $success = $userModel->updatePassword($_SESSION["user_id"], $newPassword);

        if ($success) {
            $message = "Password updated successfully.";
            $messageClass = "success-message";
        } else {
            $message = "Failed to update password. Please try again.";
            $messageClass = "error-message";
        }
    }
}

$role = $_SESSION["role"];
if ($role === "customer") {
    $backLink = "customer_dashboard.php";
} elseif ($role === "technician") {
    $backLink = "technician_dashboard.php";
} else {
    $backLink = "admin_dashboard.php";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Change Password</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

<div class="page-wrapper">
    <div class="card">
        <div class="page-header">
            <h1>Change Password</h1>
            <a href="<?php echo $backLink; ?>" class="btn btn-secondary">Back to Dashboard</a>
        </div>

        <p class="page-subtitle">Update your account password.</p>

        <?php if (!empty($message)): ?>
            <div class="<?php echo $messageClass; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="current_password">Current Password</label>
            <input id="current_password" type="password" name="current_password" required>

            <label for="new_password">New Password</label>
            <input id="new_password" type="password" name="new_password" minlength="8" required>
            <small class="field-hint">At least 8 characters with uppercase, lowercase, and a number.</small>

            <label for="confirm_password">Confirm New Password</label>
            <input id="confirm_password" type="password" name="confirm_password" minlength="8" required>

            <button type="submit">Update Password</button>
        </form>
    </div>
</div>

</body>
</html>
