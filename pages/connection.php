<?php
// Set the default timezone
date_default_timezone_set("Asia/Manila");

// Database connection parameters
$host = "localhost";
$user = "root";
$password = "";
$database = "db_barangay";

// Establish connection
$con = mysqli_connect($host, $user, $password, $database);

// Check if connection was successful
if (!$con) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>
