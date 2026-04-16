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

<h1>Welcome <?php echo htmlspecialchars($_SESSION["first_name"] ?? "Customer"); ?></h1>
<p>You are logged in.</p>

<p><a href="submit_complaint.php">Submit a Complaint</a></p>
<p><a href="view_complaints.php">View My Complaints</a></p>
<p><a href="logout.php">Logout</a></p>

</body>
</html>
