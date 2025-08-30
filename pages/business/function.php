<?php
session_start();
include "../connection.php";

// ✅ Add Business
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_business"])) {
    $business_name = mysqli_real_escape_string($con, $_POST["business_name"]);
    $owner_id = intval($_POST["owner_id"]);
    $business_type = mysqli_real_escape_string($con, $_POST["business_type"]);
    $registration_date = $_POST["registration_date"];
    $validity_period = $_POST["validity_period"];

    // Optional: Check for duplicate business under same owner
    $check = mysqli_query($con, "SELECT * FROM business_registrations WHERE business_name = '$business_name' AND owner_id = '$owner_id'");
    if (mysqli_num_rows($check) > 0) {
        $_SESSION["error"] = "This business is already registered under the selected owner.";
    } else {
        $query = "INSERT INTO business_registrations 
                  (owner_id, business_name, business_type, registration_date, validity_period) 
                  VALUES ('$owner_id', '$business_name', '$business_type', '$registration_date', '$validity_period')";

        if (mysqli_query($con, $query)) {
            $_SESSION["message"] = "Business added successfully.";
        } else {
            $_SESSION["error"] = "Error adding business: " . mysqli_error($con);
        }
    }

    header("Location: business_registration.php");
    exit();
}

// ✅ Update Business
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_business"])) {
    $business_id = intval($_POST["business_id"]);
    $business_name = mysqli_real_escape_string($con, $_POST["business_name"]);
    $business_type = mysqli_real_escape_string($con, $_POST["business_type"]);
    $registration_date = $_POST["registration_date"];
    $validity_period = $_POST["validity_period"];

    $query = "UPDATE business_registrations SET 
              business_name = '$business_name',
              business_type = '$business_type',
              registration_date = '$registration_date',
              validity_period = '$validity_period'
              WHERE business_id = '$business_id'";

    if (mysqli_query($con, $query)) {
        $_SESSION["message"] = "Business updated successfully.";
    } else {
        $_SESSION["error"] = "Error updating business: " . mysqli_error($con);
    }

    header("Location: business_registration.php");
    exit();
}
?>
