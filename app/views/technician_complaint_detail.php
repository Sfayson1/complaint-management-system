<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "technician") {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/Technician.php';

$db = new Database();
$conn = $db->connect();

$technicianModel = new Technician($conn);
$employee = $technicianModel->getEmployeeByUserId($_SESSION["user_id"]);

if (!$employee) {
    die("Technician record not found.");
}

$employeeId = $employee["employee_id"];
$complaintId = $_GET["id"] ?? null;

if (!$complaintId || !is_numeric($complaintId)) {
    die("Invalid complaint.");
}

$message = "";
$messageClass = "";

$complaint = $technicianModel->getAssignedComplaintById($complaintId, $employeeId);

if (!$complaint) {
    die("Complaint not found or not assigned to you.");
}

$isResolved = $complaint["status"] === "resolved";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["add_note"])) {
        if ($isResolved) {
            $message = "Notes cannot be added to a resolved complaint.";
            $messageClass = "error-message";
        } else {
            $noteText = trim($_POST["note_text"] ?? "");

            if (empty($noteText)) {
                $message = "Technician note cannot be empty.";
                $messageClass = "error-message";
            } elseif (strlen($noteText) > 1000) {
                $message = "Note must be 1000 characters or fewer.";
                $messageClass = "error-message";
            } else {
                $success = $technicianModel->addTechnicianNote($complaintId, $employeeId, $noteText);

                if ($success) {
                    $message = "Note added successfully.";
                    $messageClass = "success-message";
                    $complaint = $technicianModel->getAssignedComplaintById($complaintId, $employeeId);
                } else {
                    $message = "Failed to add note.";
                    $messageClass = "error-message";
                }
            }
        }
    }

    if (isset($_POST["resolve_complaint"])) {
        if ($isResolved) {
            $message = "This complaint has already been resolved.";
            $messageClass = "error-message";
        } else {
            $resolutionNotes = trim($_POST["resolution_notes"] ?? "");

            if (empty($resolutionNotes)) {
                $message = "Resolution notes are required to resolve the complaint.";
                $messageClass = "error-message";
            } elseif (strlen($resolutionNotes) > 2000) {
                $message = "Resolution notes must be 2000 characters or fewer.";
                $messageClass = "error-message";
            } else {
                $success = $technicianModel->resolveComplaint($complaintId, $resolutionNotes);

                if ($success) {
                    $message = "Complaint marked as resolved.";
                    $messageClass = "success-message";
                    $complaint = $technicianModel->getAssignedComplaintById($complaintId, $employeeId);
                    $isResolved = true;
                } else {
                    $message = "Failed to resolve complaint.";
                    $messageClass = "error-message";
                }
            }
        }
    }
}

$notes = $technicianModel->getTechnicianNotes($complaintId);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Technician Complaint Detail</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

<div class="admin-page-wrapper">
    <div class="card">
        <div class="page-header">
            <h1>Complaint #<?php echo htmlspecialchars($complaint["complaint_id"]); ?></h1>
            <a href="technician_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>

        <p class="page-subtitle">Review complaint details, add notes, and resolve the issue.</p>

        <?php if (!empty($message)): ?>
            <div class="<?php echo $messageClass; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div class="detail-grid">
            <div class="detail-card">
                <h2>Customer Information</h2>
                <p><strong>Name:</strong> <?php echo htmlspecialchars($complaint["first_name"] . ' ' . $complaint["last_name"]); ?></p>
                <p><strong>Phone:</strong> <?php echo htmlspecialchars($complaint["phone"]); ?></p>
                <p><strong>Address:</strong>
                    <?php echo htmlspecialchars(
                        $complaint["street_address"] . ', ' .
                        $complaint["city"] . ', ' .
                        $complaint["state"] . ' ' .
                        $complaint["zip_code"]
                    ); ?>
                </p>
            </div>

            <div class="detail-card">
                <h2>Complaint Details</h2>
                <p><strong>Product/Service:</strong> <?php echo htmlspecialchars($complaint["product_name"]); ?></p>
                <p><strong>Category:</strong> <?php echo htmlspecialchars($complaint["category_name"]); ?></p>
                <p>
                    <strong>Status:</strong>
                    <span class="status-badge status-<?php echo htmlspecialchars(strtolower($complaint["status"])); ?>">
                        <?php echo htmlspecialchars($complaint["status"]); ?>
                    </span>
                </p>
                <p><strong>Date Submitted:</strong> <?php echo htmlspecialchars(date("M j, Y", strtotime($complaint["created_at"]))); ?></p>

                <?php if (!empty($complaint["resolution_date"])): ?>
                    <p><strong>Resolved On:</strong> <?php echo htmlspecialchars(date("M j, Y", strtotime($complaint["resolution_date"]))); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="detail-card" style="margin-top: 24px;">
            <h2>Description</h2>
            <p><?php echo htmlspecialchars($complaint["description"]); ?></p>
        </div>

        <div class="detail-card" style="margin-top: 24px;">
            <h2>Uploaded Image</h2>
            <?php if (!empty($complaint["file_path"])): ?>
                <img src="<?php echo htmlspecialchars($complaint["file_path"]); ?>" alt="Complaint image" class="complaint-image">
            <?php else: ?>
                <p class="empty-state-text">No image uploaded for this complaint.</p>
            <?php endif; ?>
        </div>

        <div class="detail-card" style="margin-top: 24px;">
            <h2>Technician Notes</h2>

            <?php if (empty($notes)): ?>
                <p class="empty-state-text">No technician notes yet.</p>
            <?php else: ?>
                <?php foreach ($notes as $note): ?>
                    <div class="note-card">
                        <p><strong><?php echo htmlspecialchars($note["first_name"] . ' ' . $note["last_name"]); ?></strong></p>
                        <p><?php echo htmlspecialchars($note["note"] ?? ""); ?></p>
                        <small><?php echo htmlspecialchars(date("M j, Y g:i A", strtotime($note["created_at"]))); ?></small>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if (!$isResolved): ?>
                <form method="POST" action="">
                    <label for="note_text">Add Technician Note</label>
                    <textarea name="note_text" id="note_text" required></textarea>
                    <button type="submit" name="add_note">Save Note</button>
                </form>
            <?php endif; ?>
        </div>

        <?php if (!$isResolved): ?>
            <div class="detail-card" style="margin-top: 24px;">
                <h2>Resolve Complaint</h2>
                <form method="POST" action="">
                    <label for="resolution_notes">Resolution Notes</label>
                    <textarea name="resolution_notes" id="resolution_notes" required></textarea>
                    <button type="submit" name="resolve_complaint">Mark as Resolved</button>
                </form>
            </div>
        <?php else: ?>
            <div class="detail-card" style="margin-top: 24px;">
                <h2>Resolution Notes</h2>
                <div class="resolution-notes">
                    <?php echo htmlspecialchars($complaint["resolution_notes"] ?? "No resolution notes provided."); ?>
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>

</body>
</html>
