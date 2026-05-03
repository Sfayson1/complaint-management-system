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

$firstName = $lastName = $email = $role = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $firstName = trim($_POST["first_name"] ?? "");
    $lastName = trim($_POST["last_name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $role = $_POST["role"] ?? "";
    $password = $_POST["password"] ?? "";
    $confirmPassword = $_POST["confirm_password"] ?? "";

    if (empty($firstName) || empty($lastName) || empty($email) || empty($role) || empty($password) || empty($confirmPassword)) {
        $message = "All fields are required.";
        $messageClass = "error-message";
    } elseif (strlen($firstName) > 50 || strlen($lastName) > 50) {
        $message = "First and last name must be 50 characters or fewer.";
        $messageClass = "error-message";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 100) {
        $message = "Please enter a valid email address.";
        $messageClass = "error-message";
    } elseif (!in_array($role, ["technician", "administrator"])) {
        $message = "Role must be Technician or Administrator.";
        $messageClass = "error-message";
    } elseif (
        strlen($password) < 8 ||
        !preg_match('/[A-Z]/', $password) ||
        !preg_match('/[a-z]/', $password) ||
        !preg_match('/[0-9]/', $password)
    ) {
        $message = "Password must be at least 8 characters and include uppercase, lowercase, and a number.";
        $messageClass = "error-message";
    } elseif ($password !== $confirmPassword) {
        $message = "Passwords do not match.";
        $messageClass = "error-message";
    } else {
        try {
            $success = $adminModel->addEmployee($email, $password, $role, $firstName, $lastName);

            if ($success) {
                header("Location: admin_view_employees.php");
                exit();
            } else {
                $message = "Failed to add employee. Please try again.";
                $messageClass = "error-message";
            }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $message = "That email address is already registered.";
            } else {
                $message = "Error: " . $e->getMessage();
            }
            $messageClass = "error-message";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Employee</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

<div class="page-wrapper">
    <div class="card">
        <div class="page-header">
            <h1>Add Employee</h1>
            <a href="admin_view_employees.php" class="btn btn-secondary">Back to Employees</a>
        </div>

        <p class="page-subtitle">Create a new technician or administrator account.</p>

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

            <label for="email">Email</label>
            <input id="email" type="email" name="email" maxlength="100" value="<?php echo htmlspecialchars($email); ?>" required>

            <label for="role">Role</label>
            <select id="role" name="role" required>
                <option value="">-- Select Role --</option>
                <option value="technician" <?php echo $role === "technician" ? "selected" : ""; ?>>Technician</option>
                <option value="administrator" <?php echo $role === "administrator" ? "selected" : ""; ?>>Administrator</option>
            </select>

            <label for="password">Password</label>
            <input id="password" type="password" name="password" minlength="8" required>
            <small class="field-hint">At least 8 characters with uppercase, lowercase, and a number.</small>

            <label for="confirm_password">Confirm Password</label>
            <input id="confirm_password" type="password" name="confirm_password" minlength="8" required>

            <button type="submit">Add Employee</button>
        </form>
    </div>
</div>

</body>
</html>
