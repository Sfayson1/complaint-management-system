<?php
require_once __DIR__ . '/../config/database.php';

$db = new Database();
$conn = $db->connect();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Home</title>
</head>
<body>
    <h1>Complaint Management System</h1>
    <p>Database connected successfully.</p>

    <a href="register.php">Register</a> |
    <a href="login.php">Login</a>
</body>
</html>
