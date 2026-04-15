<?php
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "customer") {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Customer Dashboard</title>
</head>
<body>

<h1>Welcome <?php echo htmlspecialchars($_SESSION["first_name"]); ?></h1>
<p>You are logged in.</p>

<a href="logout.php">Logout</a>

</body>
</html>
