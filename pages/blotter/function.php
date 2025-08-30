<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include '../connection.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

// ✅ ADD BLOTTER RECORD
if (isset($_POST['add_blotter'])) {
    $complainant_id = intval($_POST['complainant_id']); // Use intval for numeric ID
    $respondent_id = intval($_POST['respondent_id']);   // Use intval for numeric ID
    $incident_date = mysqli_real_escape_string($con, $_POST['incident_date']);
    $incident_desc = mysqli_real_escape_string($con, $_POST['incident_desc']);
    $status = 'Pending'; // Default status for new records

    // Validate that complainant and respondent are not the same
    if ($complainant_id == $respondent_id) {
        $_SESSION['error'] = 'Complainant and Respondent cannot be the same person.';
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }

    // Validate required fields
    if (empty($complainant_id) || empty($respondent_id) || empty($incident_date) || empty($incident_desc)) {
        $_SESSION['error'] = 'All fields are required.';
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }

    $query = "INSERT INTO blotter_records (complainant_id, respondent_id, incident_date, incident_desc, status) 
              VALUES ('$complainant_id', '$respondent_id', '$incident_date', '$incident_desc', '$status')";

    if (mysqli_query($con, $query)) {
        $_SESSION['success_add'] = true;
    } else {
        $_SESSION['error'] = 'Error adding blotter record: ' . mysqli_error($con);
    }
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}

// ✅ EDIT BLOTTER RECORD
if (isset($_POST['edit_blotter'])) {
    $blotter_id = intval($_POST['blotter_id']); // Use intval for numeric ID
    $complainant_id = intval($_POST['edit_complainant_id']);
    $respondent_id = intval($_POST['edit_respondent_id']);
    $incident_date = mysqli_real_escape_string($con, $_POST['edit_incident_date']);
    $incident_desc = mysqli_real_escape_string($con, $_POST['edit_incident_desc']);
    $status = mysqli_real_escape_string($con, $_POST['edit_status']); // Handle status field

    // Validate that complainant and respondent are not the same
    if ($complainant_id == $respondent_id) {
        $_SESSION['error'] = 'Complainant and Respondent cannot be the same person.';
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }

    // Validate required fields
    if (empty($complainant_id) || empty($respondent_id) || empty($incident_date) || empty($incident_desc) || empty($status)) {
        $_SESSION['error'] = 'All fields are required.';
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }

    // Validate status value
    if (!in_array($status, ['Pending', 'Solved'])) {
        $_SESSION['error'] = 'Invalid status value.';
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }

    $query = "UPDATE blotter_records 
              SET complainant_id = '$complainant_id', 
                  respondent_id = '$respondent_id', 
                  incident_date = '$incident_date', 
                  incident_desc = '$incident_desc',
                  status = '$status'
              WHERE blotter_id = '$blotter_id'";

    if (mysqli_query($con, $query)) {
        $_SESSION['success_edit'] = true;
    } else {
        $_SESSION['error'] = 'Error updating blotter record: ' . mysqli_error($con);
    }
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}
?>