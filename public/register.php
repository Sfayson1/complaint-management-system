<?php
require_once __DIR__ . '/../config/database.php';

$db = new Database();
$conn = $db->connect();

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $firstName = trim($_POST["first_name"]);
    $lastName = trim($_POST["last_name"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $password = $_POST["password"];

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    try {
        $stmt = $conn->prepare("
            INSERT INTO users (email, password_hash, role)
            VALUES (?, ?, 'customer')
        ");
        $stmt->execute([$email, $hashedPassword]);

        $userId = $conn->lastInsertId();

        $stmt = $conn->prepare("
            INSERT INTO customers (user_id, first_name, last_name, phone)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$userId, $firstName, $lastName, $phone]);

        $message = "Registration successful!";
    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
</head>
<body>
    <h1>Customer Registration</h1>

    <?php if (!empty($message)): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <label>First Name:</label><br>
        <input type="text" name="first_name" required><br><br>

        <label>Last Name:</label><br>
        <input type="text" name="last_name" required><br><br>

        <label>Email:</label><br>
        <input type="email" name="email" required><br><br>

        <label>Phone:</label><br>
        <input type="text" name="phone" required><br><br>

        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>

        <button type="submit">Register</button>
    </form>

    <p><a href="index.php">Back to Home</a></p>
</body>
</html>
