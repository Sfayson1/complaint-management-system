<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "customer") {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/controllers/ComplaintController.php';
require_once __DIR__ . '/../app/models/Customer.php';

$db = new Database();
$conn = $db->connect();

$complaintController = new ComplaintController($conn);
$customerModel = new Customer($conn);

$message = "";

// Still okay to load dropdown data here for now
$productStmt = $conn->query("SELECT product_service_id, name FROM products_services ORDER BY name");
$products = $productStmt->fetchAll(PDO::FETCH_ASSOC);

$categoryStmt = $conn->query("SELECT category_id, name FROM complaint_categories ORDER BY name");
$categories = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);

// Get customer_id from logged-in user using model
$customer = $customerModel->getByUserId($_SESSION["user_id"]);

if (!$customer) {
    die("Customer record not found.");
}

$customerId = $customer["customer_id"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $productServiceId = $_POST["product_service_id"] ?? "";
    $categoryId = $_POST["category_id"] ?? "";
    $description = trim($_POST["description"] ?? "");

    if (empty($productServiceId) || empty($categoryId) || empty($description)) {
        $message = "All fields are required.";
    } else {
        $success = $complaintController->submitComplaint(
            $customerId,
            $productServiceId,
            $categoryId,
            $description
        );

        $message = $success ? "Complaint submitted successfully." : "Failed to submit complaint.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Submit Complaint</title>
</head>
<body>

<h1>Submit a Complaint</h1>

<?php if (!empty($message)): ?>
    <p><?php echo htmlspecialchars($message); ?></p>
<?php endif; ?>

<form method="POST" action="">
    <label>Product / Service:</label><br>
    <select name="product_service_id" required>
        <option value="">-- Select a Product/Service --</option>
        <?php foreach ($products as $product): ?>
            <option value="<?php echo $product["product_service_id"]; ?>">
                <?php echo htmlspecialchars($product["name"]); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <br><br>

    <label>Complaint Category:</label><br>
    <select name="category_id" required>
        <option value="">-- Select a Category --</option>
        <?php foreach ($categories as $category): ?>
            <option value="<?php echo $category["category_id"]; ?>">
                <?php echo htmlspecialchars($category["name"]); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <br><br>

    <label>Description:</label><br>
    <textarea name="description" rows="6" cols="50" required></textarea>
    <br><br>

    <button type="submit">Submit Complaint</button>
</form>

<p><a href="customer_dashboard.php">Back to Dashboard</a></p>

</body>
</html>
