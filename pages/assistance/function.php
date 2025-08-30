<?php
include "../connection.php";

if (isset($_POST["add_request"])) {
    $resident_id = $_POST["resident_id"] ?? null;
    $service_type_id = $_POST["service_type_id"] ?? null;
    $request_date = $_POST["request_date"] ?? null;
    $status = $_POST["status"] ?? "Pending";

    // Check resident existence
    $resident_check = $con->prepare("SELECT id FROM tblresident WHERE id = ?");
    $resident_check->bind_param("i", $resident_id);
    $resident_check->execute();
    $resident_check->store_result();

    if ($resident_check->num_rows == 0) {
        $_SESSION["error"] = "Resident ID does not exist.";
        header("Location: assistance_requests.php");
        exit();
    }

    // Upload file
    $document_path = null;
    if (!empty($_FILES["document"]["name"])) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $file_name = time() . "_" . basename($_FILES["document"]["name"]);
        $document_path = $target_dir . $file_name;
        move_uploaded_file($_FILES["document"]["tmp_name"], $document_path);
    }

    // Insert into DB
    $sql = $con->prepare("INSERT INTO assistance_requests (resident_id, service_type_id, request_date, status, document_path) 
                          VALUES (?, ?, ?, ?, ?)");
    $sql->bind_param("iisss", $resident_id, $service_type_id, $request_date, $status, $document_path);

    if ($sql->execute()) {
        $_SESSION["success"] = "Request added successfully.";
    } else {
        $_SESSION["error"] = "Error adding request: " . $sql->error;
    }

    header("Location: assistance_requests.php");
    exit();
}

// ✅ FIXED EDIT REQUEST FUNCTION (CAN UPDATE DOCUMENT)
if (isset($_POST["update_request"])) {
    $request_id = $_POST["request_id"];
    $resident_id = $_POST["resident_id"];
    $service_type_id = $_POST["service_type_id"];
    $request_date = $_POST["request_date"];
    $status = $_POST["status"];

    // ✅ Get current document path (if any)
    $query = $con->prepare("SELECT document_path FROM assistance_requests WHERE request_id = ?");
    $query->bind_param("i", $request_id);
    $query->execute();
    $query->bind_result($current_document);
    $query->fetch();
    $query->close();

    // ✅ Handle document update
    if (!empty($_FILES["document"]["name"])) {
        $target_dir = "uploads/";

        // Create directory if it doesn't exist
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $file_name = time() . "_" . basename($_FILES["document"]["name"]);
        $new_document_path = $target_dir . $file_name;

        // Move uploaded file
        if (move_uploaded_file($_FILES["document"]["tmp_name"], $new_document_path)) {
            // ✅ Delete old document if it exists
            if (!empty($current_document) && file_exists($current_document)) {
                unlink($current_document);
            }
            $document_path = $new_document_path;
        } else {
            $document_path = $current_document;
        }
    } else {
        $document_path = $current_document;
    }

    // ✅ Update request in database
    $sql = $con->prepare("UPDATE assistance_requests SET resident_id=?, service_type_id=?, request_date=?, status=?, document_path=? WHERE request_id=?");
    $sql->bind_param("iisssi", $resident_id, $service_type_id, $request_date, $status, $document_path, $request_id);

    if ($sql->execute()) {
        $_SESSION['success'] = 'Request updated successfully!';
    } else {
        $_SESSION['error'] = 'Error updating request: ' . $sql->error;
    }

    header("Location: assistance_requests.php");
    exit();
}

?>
