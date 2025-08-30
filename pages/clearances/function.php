<?php
session_start();
include "../connection.php";

// Helper: Insert Notification
function insertNotification($con, $user_id, $message, $icon = 'fa fa-info-circle') {
    if (!$user_id) return;
    $stmt = $con->prepare("INSERT INTO notifications (user_id, message, icon, status) VALUES (?, ?, ?, 'unread')");
    $stmt->bind_param("iss", $user_id, $message, $icon);
    $stmt->execute();
    $stmt->close();
}

// Session-based user info
$current_user_id = $_SESSION['id'] ?? $_SESSION['userid'] ?? 0;
$current_role = $_SESSION['role'] ?? 'Unknown';

// Function to handle file upload
function uploadFile($file, $target_dir) {
    if (!isset($file) || $file["error"] === UPLOAD_ERR_NO_FILE) return "";

    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $allowed_ext = ["pdf", "jpg", "png", "doc", "docx"];
    $file_name = basename($file["name"]);
    $file_tmp = $file["tmp_name"];
    $file_size = $file["size"];
    $file_error = $file["error"];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    if (!in_array($file_ext, $allowed_ext)) {
        $_SESSION["error"] = "Invalid file type. Allowed: PDF, JPG, PNG, DOC, DOCX.";
        return false;
    }

    if ($file_size > 5 * 1024 * 1024) {
        $_SESSION["error"] = "File size exceeds the 5MB limit.";
        return false;
    }

    if ($file_error !== UPLOAD_ERR_OK) {
        $_SESSION["error"] = "Error uploading file. Code: $file_error";
        return false;
    }

    $new_file_name = uniqid("clearance_", true) . "." . $file_ext;
    $document_path = $target_dir . $new_file_name;

    if (!move_uploaded_file($file_tmp, $document_path)) {
        $_SESSION["error"] = "File upload failed. Check folder permissions.";
        return false;
    }

    return $document_path;
}

// ✅ ADD Barangay Clearance
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_clearance"])) {
    $resident_id = intval($_POST["resident_id"]);
    $clearance_type_id = intval($_POST["clearance_type_id"]);
    $issued_by = intval($_POST["issued_by"]);
    $issue_date = $_POST["issue_date"];

    $document_path = uploadFile($_FILES["document"], "../uploads/");
    if ($document_path === false) {
        header("Location: clearances.php");
        exit();
    }

    $stmt = $con->prepare("INSERT INTO barangay_clearances (resident_id, clearance_type_id, issued_by, issue_date, document_path) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iisss", $resident_id, $clearance_type_id, $issued_by, $issue_date, $document_path);

    if ($stmt->execute()) {
        $_SESSION["message"] = "Barangay Clearance added successfully.";

        $action = "Added barangay clearance for Resident ID: $resident_id";
        mysqli_query($con, "INSERT INTO tbllogs (user, logdate, action) VALUES ('$current_role', NOW(), '$action')");
        insertNotification($con, $current_user_id, $action, "fa fa-file");
    } else {
        $_SESSION["error"] = "Error adding clearance: " . $stmt->error;
    }

    $stmt->close();
    header("Location: clearances.php");
    exit();
}

// ✅ UPDATE Barangay Clearance
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_clearance"])) {
    $clearance_id = intval($_POST["clearance_id"]);
    $clearance_type_id = intval($_POST["clearance_type_id"]);
    $issued_by = intval($_POST["issued_by"]);
    $issue_date = $_POST["issue_date"];
    $document_path = $_POST["existing_document"];

    if (!empty($_FILES["document"]["name"])) {
        $new_document_path = uploadFile($_FILES["document"], "../uploads/");
        if ($new_document_path !== false) {
            $document_path = $new_document_path;
        }
    }

    $stmt = $con->prepare("UPDATE barangay_clearances SET clearance_type_id = ?, issued_by = ?, issue_date = ?, document_path = ? WHERE clearance_id = ?");
    $stmt->bind_param("iissi", $clearance_type_id, $issued_by, $issue_date, $document_path, $clearance_id);

    if ($stmt->execute()) {
        $_SESSION["message"] = "Barangay Clearance updated successfully.";

        $action = "Updated clearance ID $clearance_id";
        mysqli_query($con, "INSERT INTO tbllogs (user, logdate, action) VALUES ('$current_role', NOW(), '$action')");
        insertNotification($con, $current_user_id, $action, "fa fa-edit");
    } else {
        $_SESSION["error"] = "Error updating clearance: " . $stmt->error;
    }

    $stmt->close();
    header("Location: clearances.php");
    exit();
}

?>
