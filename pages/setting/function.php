<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include '../connection.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

// ... rest of your code

// ✅ ADD SETTING
if (isset($_POST['add_setting'])) {
    $barangay = mysqli_real_escape_string($con, $_POST['barangay']);
    $city = mysqli_real_escape_string($con, $_POST['city']);
    $contact_no = mysqli_real_escape_string($con, $_POST['contact_no']);

    $image = $_FILES['image']['name'];
    $image_tmp = $_FILES['image']['tmp_name'];
    $image_path = '../setting/uploads/' . basename($image);

    if (!empty($image) && move_uploaded_file($image_tmp, $image_path)) {
        $query = "INSERT INTO settings (image, barangay, city, contact_no)
                  VALUES ('$image', '$barangay', '$city', '$contact_no')";

        if (mysqli_query($con, $query)) {
            $_SESSION['success_add'] = true;
        } else {
            $_SESSION['error'] = "Error adding setting to the database!";
        }
    } else {
        $_SESSION['error'] = "Failed to upload image!";
    }

    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}

// ✅ EDIT SETTING
if (isset($_POST['edit_setting'])) {
    $setting_id = $_POST['setting_id'];
    $barangay = mysqli_real_escape_string($con, $_POST['edit_barangay']);
    $city = mysqli_real_escape_string($con, $_POST['edit_city']);
    $contact_no = mysqli_real_escape_string($con, $_POST['edit_contact_no']);

    $new_image = $_FILES['edit_image']['name'];
    $new_image_tmp = $_FILES['edit_image']['tmp_name'];
    $image_sql = "";

    if (!empty($new_image)) {
        $new_image_path = '../setting/uploads/' . basename($new_image);

        if (move_uploaded_file($new_image_tmp, $new_image_path)) {
            // Remove old image
            $get_old = mysqli_query($con, "SELECT image FROM settings WHERE id = '$setting_id'");
            if ($row = mysqli_fetch_assoc($get_old)) {
                $old_path = '../setting/uploads/' . $row['image'];
                if (file_exists($old_path)) {
                    unlink($old_path);
                }
            }

            $image_sql = ", image = '$new_image'";
        } else {
            $_SESSION['error'] = "Failed to upload new image!";
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        }
    }

    $query = "UPDATE settings SET barangay='$barangay', city='$city', contact_no='$contact_no' $image_sql WHERE id='$setting_id'";

    if (mysqli_query($con, $query)) {
        $_SESSION['success_edit'] = true;
    } else {
        $_SESSION['error'] = "Error updating setting!";
    }

    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}

// ✅ DELETE SETTINGS
if (isset($_POST['btn_delete'])) {
    if (isset($_POST['chk_delete']) && count($_POST['chk_delete']) > 0) {
        $errors = 0;

        foreach ($_POST['chk_delete'] as $id) {
            // Delete image file
            $get_image = mysqli_query($con, "SELECT image FROM settings WHERE id = '$id'");
            if ($row = mysqli_fetch_assoc($get_image)) {
                $image_file = '../setting/uploads/' . $row['image'];
                if (file_exists($image_file)) {
                    unlink($image_file);
                }
            }

            // Delete database record
            if (!mysqli_query($con, "DELETE FROM settings WHERE id = '$id'")) {
                $errors++;
            }
        }

        if ($errors === 0) {
            $_SESSION['delete'] = 1;
        } else {
            $_SESSION['error'] = "Some records couldn't be deleted.";
        }
    } else {
        $_SESSION['error'] = "No settings selected for deletion.";
    }

    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}
?>
