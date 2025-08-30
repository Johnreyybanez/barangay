<?php
include '../connection.php';


// ✅ ADD BUSINESS TYPE
if (isset($_POST['add_business_type'])) {
    $business_type_name = mysqli_real_escape_string($con, $_POST['business_type_name']);
    
    // Check if the business type already exists
    $check_query = "SELECT * FROM business_types WHERE business_type_name = '$business_type_name'";
    $check_result = mysqli_query($con, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        echo "<script>alert('Business Type Already Exists!'); window.location.href=document.referrer;</script>";
    } else {
        $query = "INSERT INTO business_types (business_type_name) VALUES ('$business_type_name')";
        if (mysqli_query($con, $query)) {
            echo "<script>alert('Business Type Added Successfully!'); window.location.href=document.referrer;</script>";
        } else {
            echo "<script>alert('Error Adding Business Type!'); window.location.href=document.referrer;</script>";
        }
    }
}



// ✅ EDIT SERVICE TYPE
if (isset($_POST['edit_service'])) {
    $service_type_id = $_POST['business_type_id'];
    $business_type_name = mysqli_real_escape_string($con, $_POST['edit_business_type_name']);

    $query = "UPDATE business_types SET business_type_name = ? WHERE business_type_id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("si", $business_type_name, $service_type_id);

    if ($stmt->execute()) {
        echo "<script>alert('Updated Successfully!'); window.location.href=document.referrer;</script>";
    } else {
        echo "<script>alert('Error Updating Service Type!'); window.location.href=document.referrer;</script>";
    }
}

// ✅ DELETE SERVICE TYPES
if (isset($_POST['btn_delete'])) {
    if (isset($_POST['chk_delete']) && !empty($_POST['chk_delete'])) {
        $ids_to_delete = implode(",", array_map('intval', $_POST['chk_delete'])); // sanitize inputs
        $delete_query = "DELETE FROM business_types WHERE business_type_id IN ($ids_to_delete)";
        
        if (mysqli_query($con, $delete_query)) {
            $_SESSION['delete'] = 1;
            echo "<script>alert('Deleted Successfully!'); window.location.href=document.referrer;</script>";
        } else {
            echo "<script>alert('Error Deleting Service Types!'); window.location.href=document.referrer;</script>";
        }
    } else {
        echo "<script>alert('No records selected to delete!'); window.location.href=document.referrer;</script>";
    }
}
?>
