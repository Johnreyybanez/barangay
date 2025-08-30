<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include '../connection.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

// ... rest of your code
// ✅ ADD SERVICE TYPE
if (isset($_POST['add_service'])) {
    $type_name = mysqli_real_escape_string($con, $_POST['type_name']);
    $description = mysqli_real_escape_string($con, $_POST['description']);

    // Check if the service type already exists
    $check_query = "SELECT * FROM service_types WHERE type_name = '$type_name'";
    $check_result = mysqli_query($con, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        $_SESSION['error'] = "Service Type Already Exists!";
    } else {
        $query = "INSERT INTO service_types (type_name, description) VALUES ('$type_name', '$description')";
        if (mysqli_query($con, $query)) {
            $_SESSION['success_add'] = true;
        } else {
            $_SESSION['error'] = "Error Adding Service Type!";
        }
    }

    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}

// ✅ EDIT SERVICE TYPE
if (isset($_POST['edit_service'])) {
    $service_type_id = $_POST['service_type_id'];
    $type_name = mysqli_real_escape_string($con, $_POST['edit_type_name']);
    $description = mysqli_real_escape_string($con, $_POST['edit_description']);

    $query = "UPDATE service_types SET type_name = '$type_name', description = '$description' WHERE service_type_id = '$service_type_id'";
    if (mysqli_query($con, $query)) {
        $_SESSION['success_edit'] = true;
    } else {
        $_SESSION['error'] = "Error Updating Service Type!";
    }

    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}

// ✅ DELETE SERVICE TYPE
if (isset($_POST['btn_delete'])) {
    if (isset($_POST['chk_delete']) && count($_POST['chk_delete']) > 0) {
        $errors = 0;
        foreach ($_POST['chk_delete'] as $value) {
            $delete_query = mysqli_query($con, "DELETE FROM service_types WHERE service_type_id = '$value'");
            if (!$delete_query) {
                $errors++;
            }
        }

        if ($errors === 0) {
            $_SESSION['delete'] = 1;
        } else {
            $_SESSION['error'] = "Some service types could not be deleted.";
        }
    } else {
        $_SESSION['error'] = "No service types selected for deletion.";
    }

    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}
?>
