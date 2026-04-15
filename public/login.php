<?php
session_start();

require_once __DIR__ . '/../config/database.php';

$db = new Database();
$conn = $db->connect();

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    try {
        $stmt = $conn->prepare("
            SELECT users.user_id, users.password_hash, users.role, customers.first_name
            FROM users
            LEFT JOIN customers ON users.user_id = customers.user_id
            WHERE users.email = ?
        ");
        $stmt->execute([$email]);

        $user = $stmt->fetch();

        if ($user && password_verify($password, $user["password_hash"])) {

            // store session
            $_SESSION["user_id"] = $user["user_id"];
            $_SESSION["role"] = $user["role"];
            $_SESSION["first_name"] = $user["first_name"];

            // redirect based on role
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

    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>

<h1>Login</h1>

<?php if (!empty($message)): ?>
    <p><?php echo htmlspecialchars($message); ?></p>
<?php endif; ?>

<form method="POST">
    <label>Email:</label><br>
    <input type="email" name="email" required><br><br>

    <label>Password:</label><br>
    <input type="password" name="password" required><br><br>

    <button type="submit">Login</button>
</form>

</body>
</html>
