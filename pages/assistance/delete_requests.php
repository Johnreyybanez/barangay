<?php
session_start();
include "../connection.php";

// Handle Deletion Logic
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["chk_delete"])) {
    if (!empty($_POST["chk_delete"])) {
        // Sanitize input to prevent SQL injection
        $ids = implode(",", array_map('intval', $_POST["chk_delete"]));

        // Execute deletion query
        $query = "DELETE FROM assistance_requests WHERE request_id IN ($ids)";
        if (mysqli_query($con, $query)) {
            $_SESSION["message"] = count($_POST["chk_delete"]) . " requests deleted successfully.";
        } else {
            $_SESSION["error"] = "Error deleting records: " . mysqli_error($con); // Display MySQL error
        }
    } else {
        $_SESSION["error"] = "No records selected.";
    }
} else {
    $_SESSION["error"] = "Invalid request.";
}

// Redirect back to the main page
header("Location: assistance_requests.php");
exit();
?>
