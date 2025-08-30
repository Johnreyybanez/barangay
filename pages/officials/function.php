<?php 
include '../connection.php';

function addNotification($con, $user_id, $message, $icon = 'fa fa-info-circle') {
    $stmt = $con->prepare("INSERT INTO notifications (user_id, message, icon, status, created_at) VALUES (?, ?, ?, 'unread', NOW())");
    $stmt->bind_param("iss", $user_id, $message, $icon);
    $stmt->execute();
    $stmt->close();
}

$user_id = $_SESSION['id'] ?? $_SESSION['userid'] ?? 0;

// ADD OFFICIAL
if (isset($_POST['btn_add'])) {
    $ddl_pos = trim($_POST['ddl_pos']);
    $txt_cname = trim($_POST['txt_cname']);
    $txt_contact = trim($_POST['txt_contact']);
    $txt_address = trim($_POST['txt_address']);
    $txt_sterm = date('Y-m-d', strtotime($_POST['txt_sterm']));
    $txt_eterm = date('Y-m-d', strtotime($_POST['txt_eterm']));

    $q = mysqli_query($con, "SELECT * FROM tblofficial WHERE TRIM(LOWER(sPosition)) = TRIM(LOWER('$ddl_pos'))");

    if (mysqli_num_rows($q) == 0) {
        $query = mysqli_query($con, "INSERT INTO tblofficial 
            (sPosition, completeName, pcontact, paddress, termStart, termEnd, status) 
            VALUES ('$ddl_pos', '$txt_cname', '$txt_contact', '$txt_address', '$txt_sterm', '$txt_eterm', 'Ongoing Term')") 
            or die('Error: ' . mysqli_error($con));

        if ($query) {
            $action = 'Added Official named ' . $txt_cname;
            mysqli_query($con, "INSERT INTO tbllogs (user, logdate, action) 
                VALUES ('" . $_SESSION['role'] . "', NOW(), '$action')");
            addNotification($con, $user_id, $action, 'fa fa-user-plus');

            $_SESSION['added'] = 1;
        }
    } else {
        $_SESSION['duplicate'] = 1;
    }

    header("Location: " . $_SERVER['REQUEST_URI']);
    exit();
}

// UPDATE OFFICIAL
if (isset($_POST['btn_save'])) {
    $txt_id = $_POST['hidden_id'];
    $txt_edit_cname = trim($_POST['txt_edit_cname']);
    $txt_edit_contact = trim($_POST['txt_edit_contact']);
    $txt_edit_address = trim($_POST['txt_edit_address']);
    $txt_edit_sterm = date('Y-m-d', strtotime($_POST['txt_edit_sterm']));
    $txt_edit_eterm = date('Y-m-d', strtotime($_POST['txt_edit_eterm']));

    $update_query = mysqli_query($con, "UPDATE tblofficial SET 
        completeName = '$txt_edit_cname', 
        pcontact = '$txt_edit_contact', 
        paddress = '$txt_edit_address', 
        termStart = '$txt_edit_sterm', 
        termEnd = '$txt_edit_eterm' 
        WHERE id = '$txt_id'") 
        or die('Error: ' . mysqli_error($con));

    if ($update_query) {
        $action = 'Updated Official named ' . $txt_edit_cname;
        mysqli_query($con, "INSERT INTO tbllogs (user, logdate, action) 
            VALUES ('" . $_SESSION['role'] . "', NOW(), '$action')");
        addNotification($con, $user_id, $action, 'fa fa-edit');

        $_SESSION['edited'] = 1;
    }

    header("Location: " . $_SERVER['REQUEST_URI']);
    exit();
}

// END TERM
if (isset($_POST['btn_end'])) {
    $txt_id = $_POST['hidden_id'];
    $stmt = $con->prepare("UPDATE tblofficial SET status = 'End Term' WHERE id = ?");
    $stmt->bind_param("i", $txt_id);

    if ($stmt->execute()) {
        $stmt->close();

        $action = 'Ended Term of Official ID ' . $txt_id;
        mysqli_query($con, "INSERT INTO tbllogs (user, logdate, action) 
            VALUES ('" . $_SESSION['role'] . "', NOW(), '$action')");
        addNotification($con, $user_id, $action, 'fa fa-ban');

        $_SESSION['end'] = 1;
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// START TERM
if (isset($_POST['btn_start'])) {
    $txt_id = $_POST['hidden_id'];

    $check_q = mysqli_query($con, "
        SELECT * FROM tblofficial 
        WHERE sPosition = (SELECT sPosition FROM tblofficial WHERE id = '$txt_id') 
        AND status = 'Ongoing Term' AND id != '$txt_id'");

    if (mysqli_num_rows($check_q) == 0) {
        $start_query = mysqli_query($con, "UPDATE tblofficial SET status = 'Ongoing Term' WHERE id = '$txt_id'")
            or die('Error: ' . mysqli_error($con));

        if ($start_query) {
            $action = 'Started Term of Official ID ' . $txt_id;
            mysqli_query($con, "INSERT INTO tbllogs (user, logdate, action) 
                VALUES ('" . $_SESSION['role'] . "', NOW(), '$action')");
            addNotification($con, $user_id, $action, 'fa fa-check');

            $_SESSION['start'] = 1;
        }
    } else {
        $_SESSION['duplicate'] = 1;
    }

    header("Location: " . $_SERVER['REQUEST_URI']);
    exit();
}

// DELETE OFFICIAL(S)
if (isset($_POST['btn_delete']) && isset($_POST['chk_delete'])) {
    foreach ($_POST['chk_delete'] as $value) {
        $official = mysqli_fetch_assoc(mysqli_query($con, "SELECT completeName FROM tblofficial WHERE id = '$value'"));
        $delete_query = mysqli_query($con, "DELETE FROM tblofficial WHERE id = '$value'")
            or die('Error: ' . mysqli_error($con));

        if ($delete_query) {
            $action = 'Deleted Official named ' . $official['completeName'];
            mysqli_query($con, "INSERT INTO tbllogs (user, logdate, action) 
                VALUES ('" . $_SESSION['role'] . "', NOW(), '$action')");
            addNotification($con, $user_id, $action, 'fa fa-trash');
        }
    }

    $_SESSION['delete'] = 1;
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit();
}
?>
