<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "customer") {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../controllers/ComplaintController.php';
require_once __DIR__ . '/../models/Customer.php';

$db = new Database();
$conn = $db->connect();

$complaintController = new ComplaintController($conn);
$customerModel = new Customer($conn);

$message = "";
$messageClass = "";

if (isset($_SESSION["flash_message"])) {
    $message = $_SESSION["flash_message"];
    $messageClass = $_SESSION["flash_class"];
    unset($_SESSION["flash_message"], $_SESSION["flash_class"]);
}

$productStmt = $conn->query("SELECT product_service_id, name FROM products_services ORDER BY name");
$products = $productStmt->fetchAll(PDO::FETCH_ASSOC);

$categoryStmt = $conn->query("SELECT category_id, name FROM complaint_categories ORDER BY name");
$categories = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);

$customer = $customerModel->getByUserId($_SESSION["user_id"]);

if (!$customer) {
    die("Customer record not found.");
}

$customerId = $customer["customer_id"];
$productServiceId = "";
$categoryId = "";
$description = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $productServiceId = $_POST["product_service_id"] ?? "";
    $categoryId = $_POST["category_id"] ?? "";
    $description = trim($_POST["description"] ?? "");

    if (empty($productServiceId) || empty($categoryId) || empty($description)) {
        $message = "All fields are required.";
        $messageClass = "error-message";
    } elseif (!is_numeric($productServiceId) || !is_numeric($categoryId)) {
        $message = "Invalid product or category selection.";
        $messageClass = "error-message";
    } elseif (strlen($description) > 2000) {
        $message = "Description must be 2000 characters or fewer.";
        $messageClass = "error-message";
    } else {
        $complaintId = $complaintController->submitComplaint($customerId, $productServiceId, $categoryId, $description);

        if ($complaintId) {
            if (isset($_FILES["complaint_image"]) && $_FILES["complaint_image"]["error"] === 0) {
                $allowedTypes = ["image/jpeg", "image/png", "image/gif", "image/webp"];
                $fileType = mime_content_type($_FILES["complaint_image"]["tmp_name"]);
                $fileSize = $_FILES["complaint_image"]["size"];

                if (!in_array($fileType, $allowedTypes)) {
                    $message = "Only JPG, PNG, GIF, and WEBP images are allowed.";
                    $messageClass = "error-message";
                } elseif ($fileSize > 2 * 1024 * 1024) {
                    $message = "Image must be 2MB or smaller.";
                    $messageClass = "error-message";
                } else {
                    $uploadDir = __DIR__ . '/../../assets/uploads/';
                    $fileExtension = pathinfo($_FILES["complaint_image"]["name"], PATHINFO_EXTENSION);
                    $newFileName = 'complaint_' . $complaintId . '_' . time() . '.' . $fileExtension;
                    $targetPath = $uploadDir . $newFileName;

                    if (move_uploaded_file($_FILES["complaint_image"]["tmp_name"], $targetPath)) {
                        $relativePath = '../../assets/uploads/' . $newFileName;
                        $complaintController->saveComplaintImage($complaintId, $relativePath);
                    } else {
                        $message = "Complaint saved, but image upload failed.";
                        $messageClass = "error-message";
                    }
                }
            }

            if (empty($message)) {
                $_SESSION["flash_message"] = "Complaint submitted successfully.";
                $_SESSION["flash_class"] = "success-message";
                header("Location: submit_complaint.php");
                exit();
            }
        } else {
            $message = "Failed to submit complaint.";
            $messageClass = "error-message";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Submit Complaint</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

<div class="page-wrapper">
    <div class="card">

        <div class="page-header">
            <h1>Submit Complaint</h1>
            <a href="customer_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>
        <p class="page-subtitle">Tell us what went wrong and we'll take care of it.</p>

        <?php if (!empty($message)): ?>
            <p class="<?php echo $messageClass; ?>">
                <?php echo htmlspecialchars($message); ?>
            </p>
        <?php endif; ?>

        <form method="POST" action="" enctype="multipart/form-data">
            <label for="product_service_id">Product / Service</label>
            <select name="product_service_id" id="product_service_id" required>
                <option value="">-- Select --</option>
                <?php foreach ($products as $product): ?>
                    <option value="<?php echo $product["product_service_id"]; ?>"
                        <?php echo ($productServiceId == $product["product_service_id"]) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($product["name"]); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="category_id">Category</label>
            <select name="category_id" id="category_id" required>
                <option value="">-- Select --</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category["category_id"]; ?>"
                        <?php echo ($categoryId == $category["category_id"]) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($category["name"]); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="description">Description</label>
            <small class="field-hint">Be as detailed as possible to help us resolve your issue faster.</small>
            <textarea name="description" id="description" maxlength="2000" required placeholder="Describe the issue..."><?php echo htmlspecialchars($description); ?></textarea>
            <small class="field-hint">Maximum 2000 characters.</small>

            <label for="complaint_image">Upload Image (optional)</label>
            <input id="complaint_image" type="file" name="complaint_image" accept="image/*">
            <small class="field-hint">Accepted formats: JPG, PNG, GIF, WEBP. Max size: 2MB.</small>

            <button type="submit">Submit Complaint</button>
        </form>

    </div>
</div>

</body>
</html>
