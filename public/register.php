<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../config/database.php';

$db = new Database();
$conn = $db->connect();

$message = "";
$messageClass = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $firstName = trim($_POST["first_name"] ?? "");
    $lastName = trim($_POST["last_name"] ?? "");
    $streetAddress = trim($_POST["street_address"] ?? "");
    $city = trim($_POST["city"] ?? "");
    $state = strtoupper(trim($_POST["state"] ?? ""));
    $zipCode = trim($_POST["zip_code"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    // Validation
    if (
        empty($firstName) || empty($lastName) || empty($streetAddress) ||
        empty($city) || empty($state) || empty($zipCode) ||
        empty($phone) || empty($email) || empty($password)
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
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
        $messageClass = "error-message";
    } elseif (strlen($email) > 100) {
        $message = "Email must be 100 characters or fewer.";
        $messageClass = "error-message";
    } elseif (!preg_match('/^[A-Z]{2}$/', $state)) {
        $message = "State must be exactly 2 uppercase letters (e.g., FL).";
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
    } elseif (
        strlen($password) < 8 ||
        !preg_match('/[A-Z]/', $password) ||
        !preg_match('/[a-z]/', $password) ||
        !preg_match('/[0-9]/', $password)
    ) {
        $message = "Password must be at least 8 characters and include uppercase, lowercase, and a number.";
        $messageClass = "error-message";
    } else {
        try {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insert into users
            $stmt = $conn->prepare("
                INSERT INTO users (email, password_hash, role)
                VALUES (?, ?, 'customer')
            ");
            $stmt->execute([$email, $hashedPassword]);

            $userId = $conn->lastInsertId();

            // Insert into customers
            $stmt = $conn->prepare("
                INSERT INTO customers (
                    user_id,
                    first_name,
                    last_name,
                    street_address,
                    city,
                    state,
                    zip_code,
                    phone
                )
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $userId,
                $firstName,
                $lastName,
                $streetAddress,
                $city,
                $state,
                $zipCode,
                $phone
            ]);

            $message = "Registration successful! You can now log in.";
            $messageClass = "success-message";

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
    <title>Register</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="auth-wrapper">
    <div class="card">
        <h1>Create Account</h1>
        <hr class="section-divider">

        <?php if (!empty($message)): ?>
            <div class="<?php echo $messageClass; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="first_name">First Name</label>
            <input id="first_name" type="text" name="first_name" maxlength="50" required
                   value="<?php echo htmlspecialchars($firstName ?? ''); ?>">

            <label for="last_name">Last Name</label>
            <input id="last_name" type="text" name="last_name" maxlength="50" required
                   value="<?php echo htmlspecialchars($lastName ?? ''); ?>">

            <label for="street_address">Street Address</label>
            <input id="street_address" type="text" name="street_address" maxlength="100" required
                   value="<?php echo htmlspecialchars($streetAddress ?? ''); ?>">

            <label for="city">City</label>
            <input id="city" type="text" name="city" maxlength="50" required
                   value="<?php echo htmlspecialchars($city ?? ''); ?>">

            <label for="state">State</label>
            <input id="state" type="text" name="state" maxlength="2" required
                   value="<?php echo htmlspecialchars($state ?? ''); ?>">

            <label for="zip_code">ZIP Code</label>
            <input id="zip_code" type="text" name="zip_code" maxlength="10" required
                   value="<?php echo htmlspecialchars($zipCode ?? ''); ?>">

            <label for="phone">Phone Number</label>
            <input id="phone" type="text" name="phone" placeholder="555-555-5555" maxlength="12" required
                   value="<?php echo htmlspecialchars($phone ?? ''); ?>">

            <label for="email">Email</label>
            <input id="email" type="email" name="email" maxlength="100" required
                   value="<?php echo htmlspecialchars($email ?? ''); ?>">

            <label for="password">Password</label>
            <input id="password" type="password" name="password" minlength="8" required>
            <small class="field-hint">At least 8 characters with uppercase, lowercase, and a number.</small>

            <button type="submit">Register</button>
        </form>

        <p class="auth-footer">
            Already have an account? <a href="login.php">Login here</a>
        </p>
    </div>
</div>

</body>
</html>
