<?php

include "../connection.php";


function notify($con, $user_id, $message, $icon = 'fa fa-info-circle') {
    if ($user_id) {
        $message = mysqli_real_escape_string($con, $message);
        $icon = mysqli_real_escape_string($con, $icon);
        mysqli_query($con, "INSERT INTO notifications (user_id, message, icon, status, created_at)
                          VALUES ('$user_id', '$message', '$icon', 'unread', NOW())");
    }
}

$user_id = $_SESSION['id'] ?? $_SESSION['userid'] ?? 0;

// ADD RESIDENT
if (isset($_POST['btn_add'])) {
    $txt_lname = mysqli_real_escape_string($con, $_POST['txt_lname']);
    $txt_fname = mysqli_real_escape_string($con, $_POST['txt_fname']);
    $txt_mname = mysqli_real_escape_string($con, $_POST['txt_mname']);
    $ddl_gender = $_POST['ddl_gender'];
    $txt_bdate = $_POST['txt_bdate'];
    $txt_bplace = $_POST['txt_bplace'];
    $txt_cstatus = $_POST['txt_cstatus'];
    $txt_contact = $_POST['txt_contact'];
    $txt_occp = $_POST['txt_occp'];
    $txt_religion = $_POST['txt_religion'];
    $txt_sitio = $_POST['txt_sitio'];
    $txt_purok = $_POST['txt_purok'];
    $txt_address = $_POST['txt_address'];
    $txt_pwd = $_POST['ddl_pwd'];
    $txt_senior = $_POST['ddl_senior'];
    $txt_age = date_diff(date_create($txt_bdate), date_create(date("Y-m-d")))->format('%y');
    $txt_image = 'default.png';

    if (!empty($_FILES['txt_image']['name'])) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/bmp'];
        $filetype = $_FILES['txt_image']['type'];
        $size = $_FILES['txt_image']['size'];

        if (in_array($filetype, $allowed_types) && $size <= 2048000) {
            $milliseconds = round(microtime(true) * 1000);
            $filename = $milliseconds . '_' . basename($_FILES['txt_image']['name']);
            $destination = 'image/' . $filename;
            if (!is_dir('image')) mkdir('image', 0777, true);
            move_uploaded_file($_FILES['txt_image']['tmp_name'], $destination);
            $txt_image = $filename;
        } else {
            $_SESSION['filesize'] = 1;
            header("location: " . $_SERVER['REQUEST_URI']);
            exit();
        }
    }

    $query = mysqli_query($con, "INSERT INTO tblresident (
        lname, fname, mname, bdate, bplace, age, civilstatus, occupation, religion,
        gender, contact_no, image, sitio, purok, address, pwd, senior_citizen
    ) VALUES (
        '$txt_lname', '$txt_fname', '$txt_mname', '$txt_bdate', '$txt_bplace', '$txt_age',
        '$txt_cstatus', '$txt_occp', '$txt_religion', '$ddl_gender', '$txt_contact',
        '$txt_image', '$txt_sitio', '$txt_purok', '$txt_address', '$txt_pwd', '$txt_senior'
    )") or die('Error: ' . mysqli_error($con));

    if ($query) {
        notify($con, $user_id, "New resident added: $txt_fname $txt_lname", 'fa fa-user-plus');
        $_SESSION['added'] = 1;
        header("location: " . $_SERVER['REQUEST_URI']);
        exit();
    }
}

// UPDATE RESIDENT
if (isset($_POST['btn_save'])) {
    $txt_id = $_POST['hidden_id'];
    $txt_lname = $_POST['txt_edit_lname'];
    $txt_fname = $_POST['txt_edit_fname'];
    $txt_mname = $_POST['txt_edit_mname'];
    $txt_bdate = $_POST['txt_edit_bdate'];
    $txt_bplace = $_POST['txt_edit_bplace'];
    $txt_gender = $_POST['ddl_edit_gender'];
    $txt_contact = $_POST['txt_edit_contact'];
    $txt_cstatus = $_POST['txt_edit_cstatus'];
    $txt_occp = $_POST['txt_edit_occp'];
    $txt_religion = $_POST['txt_edit_religion'];
    $txt_sitio = $_POST['txt_edit_sitio'];
    $txt_purok = $_POST['txt_edit_purok'];
    $txt_address = $_POST['txt_edit_address'];
    $txt_pwd = $_POST['ddl_edit_pwd'];
    $txt_senior = $_POST['ddl_edit_senior'];
    $txt_age = date_diff(date_create($txt_bdate), date_create(date("Y-m-d")))->format('%y');

    $chk_image = mysqli_query($con, "SELECT image FROM tblresident WHERE id = '$txt_id'");
    $rowimg = mysqli_fetch_assoc($chk_image);
    $txt_image = $rowimg['image'];

    if (!empty($_FILES['txt_edit_image']['name'])) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/bmp'];
        $filetype = $_FILES['txt_edit_image']['type'];
        $size = $_FILES['txt_edit_image']['size'];

        if (in_array($filetype, $allowed_types) && $size <= 2048000) {
            $milliseconds = round(microtime(true) * 1000);
            $filename = $milliseconds . '_' . basename($_FILES['txt_edit_image']['name']);
            move_uploaded_file($_FILES['txt_edit_image']['tmp_name'], 'image/' . $filename);
            $txt_image = $filename;
        } else {
            $_SESSION['filesize'] = 1;
            header("location: " . $_SERVER['REQUEST_URI']);
            exit();
        }
    }

    $result = mysqli_query($con, "UPDATE tblresident SET 
        lname='$txt_lname', fname='$txt_fname', mname='$txt_mname', bdate='$txt_bdate',
        bplace='$txt_bplace', age='$txt_age', civilstatus='$txt_cstatus',
        occupation='$txt_occp', religion='$txt_religion', gender='$txt_gender',
        contact_no='$txt_contact', sitio='$txt_sitio', purok='$txt_purok',
        address='$txt_address', pwd='$txt_pwd', senior_citizen='$txt_senior', image='$txt_image'
        WHERE id='$txt_id'") or die('Error: ' . mysqli_error($con));

    if ($result) {
        notify($con, $user_id, "Resident updated: $txt_fname $txt_lname", 'fa fa-edit');
        $_SESSION['edited'] = 1;
        header("location: " . $_SERVER['REQUEST_URI']);
        exit();
    }
}

// DELETE RESIDENT
if (isset($_POST['btn_delete']) && isset($_POST['chk_delete'])) {
    $deleted_count = 0;
    mysqli_autocommit($con, FALSE);

    foreach ($_POST['chk_delete'] as $id) {
        $resident_id = mysqli_real_escape_string($con, $id);
        mysqli_query($con, "DELETE FROM tblresident WHERE id = '$resident_id'");
        $deleted_count++;
    }

    mysqli_commit($con);
    mysqli_autocommit($con, TRUE);

    if ($deleted_count > 0) {
        notify($con, $user_id, "$deleted_count resident(s) deleted", 'fa fa-trash');
        $_SESSION['delete'] = 1;
    }
    header("location: " . $_SERVER['REQUEST_URI']);
    exit();
}
