<?php
session_start();
include "../connection.php";

// Helper: insert a notification
function insertNotification($con, $user_id, $message, $icon = 'fa fa-info-circle') {
    $stmt = $con->prepare("INSERT INTO notifications (user_id, message, icon, status) VALUES (?, ?, ?, 'unread')");
    $stmt->bind_param("iss", $user_id, $message, $icon);
    if (!$stmt->execute()) {
        error_log("Notification insert failed: " . $stmt->error);
    }
    $stmt->close();
}

// Get session user ID
$current_user_id = $_SESSION['id'] ?? $_SESSION['userid'] ?? 0;

// Handle Add Report Request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_report"])) {
    $report_type = mysqli_real_escape_string($con, $_POST["report_type"]);
    $amount = mysqli_real_escape_string($con, $_POST["amount"]);
    $description = mysqli_real_escape_string($con, $_POST["description"]);
    $transaction_date = mysqli_real_escape_string($con, $_POST["transaction_date"]);
    $balance = mysqli_real_escape_string($con, $_POST["balance"]);

    // Initialize document path as an empty string
    $document_path = "";

    // File Upload Handling
    if (!empty($_FILES["document"]["name"])) {
        $target_dir = "../uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $file_name = basename($_FILES["document"]["name"]);
        $target_file = $target_dir . time() . "_" . $file_name;
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $allowed_types = ["pdf", "doc", "docx", "jpg", "png"];
        if (in_array($file_type, $allowed_types)) {
            if (move_uploaded_file($_FILES["document"]["tmp_name"], $target_file)) {
                $document_path = $target_file;
            } else {
                $_SESSION["error"] = "Error uploading document.";
                header("Location: financial_reports.php");
                exit();
            }
        } else {
            $_SESSION["error"] = "Invalid file type. Only PDF, DOC, DOCX, JPG, and PNG are allowed.";
            header("Location: financial_reports.php");
            exit();
        }
    }

    // Insert the new report
    $query = "INSERT INTO financial_reports (report_type, amount, description, transaction_date, balance, document_path) 
              VALUES ('$report_type', '$amount', '$description', '$transaction_date', '$balance', '$document_path')";

    if (mysqli_query($con, $query)) {
        // Log the action
        $action = "Added financial report: $report_type - ₱$amount";
        mysqli_query($con, "INSERT INTO tbllogs (user, logdate, action) VALUES ('" . $_SESSION['role'] . "', NOW(), '$action')");

        // Notify current user
        insertNotification($con, $current_user_id, $action, "fa fa-file-invoice");

        $_SESSION["message"] = "Financial report added successfully.";
    } else {
        $_SESSION["error"] = "Error adding report: " . mysqli_error($con);
    }

    header("Location: financial_reports.php");
    exit();
}
?>
