<?php
include '../../pages/connection.php'; // adjust as needed

function clean($con, $value) {
    return mysqli_real_escape_string($con, trim($value));
}

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

// ADD USER
if (isset($_POST['btn_add'])) {
    $txt_name = clean($con, $_POST['txt_name']);
    $txt_uname = clean($con, $_POST['txt_uname']);
    $txt_pass = password_hash($_POST['txt_pass'], PASSWORD_DEFAULT);
    $txt_role = clean($con, $_POST['txt_role']);
    $imagePath = null;

    // Handle image upload
    if (isset($_FILES['txt_image']) && $_FILES['txt_image']['error'] == UPLOAD_ERR_OK) {
        $uploadDir = '../../img/';
        $imageName = basename($_FILES['txt_image']['name']);
        $imagePath = $uploadDir . $imageName;
        move_uploaded_file($_FILES['txt_image']['tmp_name'], $imagePath);
    }

    $su = mysqli_query($con, "SELECT * FROM tbluser WHERE username = '$txt_uname'");
    if (mysqli_num_rows($su) == 0) {
        $query = mysqli_query($con, "INSERT INTO tbluser (fullname, username, password_hash, role, image) VALUES ('$txt_name', '$txt_uname', '$txt_pass', '$txt_role', '$imagePath')");

        if ($query && $current_user_id) {
            $action = "Added user: $txt_name";
            mysqli_query($con, "INSERT INTO tbllogs (user, logdate, action) VALUES ('" . $_SESSION['role'] . "', NOW(), '$action')");
            insertNotification($con, $current_user_id, $action, "fa fa-user-plus");
        }
        $_SESSION['added'] = 1;
    } else {
        $_SESSION['duplicateuser'] = 1;
    }

    header("Location: " . $_SERVER['REQUEST_URI']);
    exit();
}

// EDIT USER
if (isset($_POST['btn_save'])) {
    $txt_id = clean($con, $_POST['hidden_id']);
    $txt_edit_name = clean($con, $_POST['txt_edit_name']);
    $txt_edit_uname = clean($con, $_POST['txt_edit_uname']);
    $txt_edit_pass = $_POST['txt_edit_pass'];
    $txt_edit_role = clean($con, $_POST['txt_edit_role']);
    $imagePath = null;

    // Image upload
    if (isset($_FILES['txt_edit_image']) && $_FILES['txt_edit_image']['error'] == UPLOAD_ERR_OK) {
        $uploadDir = '../../img/';
        $imageName = basename($_FILES['txt_edit_image']['name']);
        $imagePath = $uploadDir . $imageName;
        move_uploaded_file($_FILES['txt_edit_image']['tmp_name'], $imagePath);
    }

    $su = mysqli_query($con, "SELECT * FROM tbluser WHERE username = '$txt_edit_uname' AND id != '$txt_id'");
    if (mysqli_num_rows($su) == 0) {
        $update_fields = "fullname = '$txt_edit_name', username = '$txt_edit_uname', role = '$txt_edit_role'";
        if (!empty($txt_edit_pass)) {
            $hashed_pass = password_hash($txt_edit_pass, PASSWORD_DEFAULT);
            $update_fields .= ", password_hash = '$hashed_pass'";
        }
        if (!empty($imagePath)) {
            $update_fields .= ", image = '$imagePath'";
        }

        $update_query = mysqli_query($con, "UPDATE tbluser SET $update_fields WHERE id = '$txt_id'");

        if ($update_query && $current_user_id) {
            $action = "Updated user: $txt_edit_name";
            mysqli_query($con, "INSERT INTO tbllogs (user, logdate, action) VALUES ('" . $_SESSION['role'] . "', NOW(), '$action')");
            insertNotification($con, $current_user_id, $action, "fa fa-user-edit");
        }
        $_SESSION['edited'] = 1;
    } else {
        $_SESSION['duplicateuser'] = 1;
    }

    header("Location: " . $_SERVER['REQUEST_URI']);
    exit();
}

// DELETE USER
if (isset($_POST['btn_delete']) && isset($_POST['chk_delete'])) {
    foreach ($_POST['chk_delete'] as $value) {
        $value = clean($con, $value);
        $delete_query = mysqli_query($con, "DELETE FROM tbluser WHERE id = '$value'");
        if ($delete_query && $current_user_id) {
            $action = "Deleted user with ID: $value";
            mysqli_query($con, "INSERT INTO tbllogs (user, logdate, action) VALUES ('" . $_SESSION['role'] . "', NOW(), '$action')");
            insertNotification($con, $current_user_id, $action, "fa fa-trash");
        }
    }
    $_SESSION['delete'] = 1;
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit();
}
?>
