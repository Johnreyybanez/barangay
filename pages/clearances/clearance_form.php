<?php
session_start();
include "../connection.php";

// Get parameters from URL
$clearance_id = isset($_GET['clearance_id']) ? intval($_GET['clearance_id']) : 0;
$clearance_type_id = isset($_GET['clearance_type_id']) ? intval($_GET['clearance_type_id']) : 0;

// Validate input
if ($clearance_id <= 0 || $clearance_type_id <= 0) {
    $_SESSION['error'] = "Invalid parameters provided.";
    header("Location: clearances.php");
    exit();
}

// Get clearance and resident data
$query = "SELECT bc.*, 
                 CONCAT(r.fname, ' ', r.lname) AS resident_name,
                 r.*,
                 ct.type_name AS clearance_type,
                 CONCAT(o.sPosition, ' ', o.completeName) AS issued_by_name
          FROM barangay_clearances bc
          JOIN tblresident r ON bc.resident_id = r.id
          JOIN clearance_types ct ON bc.clearance_type_id = ct.clearance_type_id
          JOIN tblofficial o ON bc.issued_by = o.id
          WHERE bc.clearance_id = ?";

$stmt = $con->prepare($query);
$stmt->bind_param("i", $clearance_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error'] = "Clearance not found.";
    header("Location: clearances.php");
    exit();
}

$clearance = $result->fetch_assoc();

// Create directory for templates if it doesn't exist
$template_dir = "templates";
if (!is_dir($template_dir)) {
    mkdir($template_dir, 0755, true);
}

// Determine which template to load based on clearance type
switch ($clearance_type_id) {
    case 1: // Barangay Clearance
        include "generate_clearance.php";
        break;
   
    
    case 3: // Business Permit
        include "indigency.php";
        break;
    
    default:
        $_SESSION['error'] = "Unknown clearance type.";
        header("Location: clearances.php");
        exit();
}
?>