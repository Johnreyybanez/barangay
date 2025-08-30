<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include '../connection.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

// ✅ ADD CLEARANCE TYPE
if (isset($_POST['add_clearance'])) {
    $type_name = mysqli_real_escape_string($con, $_POST['type_name']);
    $description = mysqli_real_escape_string($con, $_POST['description']);

    $check_query = "SELECT * FROM clearance_types WHERE type_name = '$type_name'";
    $check_result = mysqli_query($con, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        $_SESSION['error'] = "Clearance Type Already Exists!";
    } else {
        $query = "INSERT INTO clearance_types (type_name, description) VALUES ('$type_name', '$description')";
        if (mysqli_query($con, $query)) {
            $_SESSION['success_add'] = true;
        } else {
            $_SESSION['error'] = "Error Adding Clearance Type!";
        }
    }

    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}

// ✅ EDIT CLEARANCE TYPE
if (isset($_POST['edit_clearance'])) {
    $clearance_type_id = $_POST['clearance_type_id'];
    $type_name = mysqli_real_escape_string($con, $_POST['edit_type_name']);
    $description = mysqli_real_escape_string($con, $_POST['edit_description']);

    $query = "UPDATE clearance_types SET type_name = '$type_name', description = '$description' WHERE clearance_type_id = '$clearance_type_id'";
    if (mysqli_query($con, $query)) {
        $_SESSION['success_edit'] = true;
    } else {
        $_SESSION['error'] = "Error Updating Clearance Type!";
    }

    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}

// ✅ DELETE CLEARANCE TYPE
if (isset($_POST['btn_delete'])) {
    if (isset($_POST['chk_delete']) && count($_POST['chk_delete']) > 0) {
        $errors = 0;
        foreach ($_POST['chk_delete'] as $value) {
            $delete_query = mysqli_query($con, "DELETE FROM clearance_types WHERE clearance_type_id = '$value'");
            if (!$delete_query) {
                $errors++;
            }
        }

        if ($errors === 0) {
            $_SESSION['delete'] = 1;
        } else {
            $_SESSION['error'] = "Some clearance types could not be deleted.";
        }
    } else {
        $_SESSION['error'] = "No clearance types selected for deletion.";
    }

    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}
?>
