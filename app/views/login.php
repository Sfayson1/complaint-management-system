<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../controllers/AuthController.php';

$db = new Database();
$conn = $db->connect();

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if (empty($email) || empty($password)) {
        $message = "Email and password are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
    } else {
        $auth = new AuthController($conn);
        $user = $auth->login($email, $password);

        if ($user) {
            if ($user["role"] === "customer") {
                header("Location: customer_dashboard.php");
            } elseif ($user["role"] === "technician") {
                header("Location: technician_dashboard.php");
            } else {
                header("Location: admin_dashboard.php");
            }
            exit();
        } else {
            $message = "Invalid email or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

<div class="auth-wrapper">
    <div class="card">
        <h1>Login</h1>
        <hr class="section-divider">

        <?php if (!empty($message)): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="email">Email</label>
            <input id="email" type="email" name="email" maxlength="100" required
                   value="<?php echo htmlspecialchars($_POST["email"] ?? ""); ?>">

            <label for="password">Password</label>
            <input id="password" type="password" name="password" required>

            <button type="submit">Login</button>
        </form>

        <p class="auth-footer">
            Don't have an account? <a href="register.php">Register here</a>
        </p>
    </div>
</div>

</body>
</html>
