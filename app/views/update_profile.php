<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "customer") {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/Customer.php';

$db = new Database();
$conn = $db->connect();

$customerModel = new Customer($conn);

$message = "";
$messageClass = "";

if (isset($_GET["success"])) {
    $message = "Profile updated successfully.";
    $messageClass = "success-message";
}

$customer = $customerModel->getByUserId($_SESSION["user_id"]);

if (!$customer) {
    die("Customer record not found.");
}

$firstName = $customer["first_name"] ?? "";
$lastName = $customer["last_name"] ?? "";
$streetAddress = $customer["street_address"] ?? "";
$city = $customer["city"] ?? "";
$state = $customer["state"] ?? "";
$zipCode = $customer["zip_code"] ?? "";
$phone = $customer["phone"] ?? "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $firstName = trim($_POST["first_name"] ?? "");
    $lastName = trim($_POST["last_name"] ?? "");
    $streetAddress = trim($_POST["street_address"] ?? "");
    $city = trim($_POST["city"] ?? "");
    $state = strtoupper(trim($_POST["state"] ?? ""));
    $zipCode = trim($_POST["zip_code"] ?? "");
    $phone = trim($_POST["phone"] ?? "");

    if (
        empty($firstName) || empty($lastName) || empty($streetAddress) ||
        empty($city) || empty($state) || empty($zipCode) || empty($phone)
    ) {
        $message = "All fields are required.";
        $messageClass = "error-message";
    } elseif (strlen($firstName) > 50 || strlen($lastName) > 50) {
        $message = "First name and last name must be 50 characters or fewer.";
        $messageClass = "error-message";
    } elseif (strlen($streetAddress) > 100) {
        $message = "Street address must be 100 characters or fewer.";
        $messageClass = "error-message";
    } elseif (strlen($city) > 50) {
        $message = "City must be 50 characters or fewer.";
        $messageClass = "error-message";
    } elseif (!preg_match('/^[A-Z]{2}$/', $state)) {
        $message = "State must be a 2-letter abbreviation.";
        $messageClass = "error-message";
    } elseif (!preg_match('/^\d{5}(-\d{4})?$/', $zipCode)) {
        $message = "Please enter a valid ZIP code.";
        $messageClass = "error-message";
    } elseif (!preg_match('/^\d{3}-\d{3}-\d{4}$/', $phone)) {
        $message = "Phone number must be in the format 555-555-5555.";
        $messageClass = "error-message";
    } elseif (strlen($phone) > 20) {
        $message = "Phone number is too long.";
        $messageClass = "error-message";
    } else {
        $success = $customerModel->updateByUserId(
            $_SESSION["user_id"],
            $firstName, $lastName, $streetAddress, $city, $state, $zipCode, $phone
        );

        if ($success) {
            header("Location: update_profile.php?success=1");
            exit();
        } else {
            $message = "Failed to update profile.";
            $messageClass = "error-message";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Profile</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

<div class="page-wrapper">
    <div class="card">
        <div class="page-header">
            <h1>Update My Profile</h1>
            <a href="customer_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>

        <p class="page-subtitle">Keep your account information up to date.</p>

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

            <label for="street_address">Street Address</label>
            <input id="street_address" type="text" name="street_address" maxlength="100" value="<?php echo htmlspecialchars($streetAddress); ?>" required>

            <label for="city">City</label>
            <input id="city" type="text" name="city" maxlength="50" value="<?php echo htmlspecialchars($city); ?>" required>

            <label for="state">State</label>
            <input id="state" type="text" name="state" maxlength="2" value="<?php echo htmlspecialchars($state); ?>" required>

            <label for="zip_code">ZIP Code</label>
            <input id="zip_code" type="text" name="zip_code" maxlength="10" value="<?php echo htmlspecialchars($zipCode); ?>" required>

            <label for="phone">Phone Number</label>
            <input id="phone" type="text" name="phone" maxlength="12" placeholder="555-555-5555" value="<?php echo htmlspecialchars($phone); ?>" required>

            <button type="submit">Save Changes</button>
        </form>
    </div>
</div>

</body>
</html>
