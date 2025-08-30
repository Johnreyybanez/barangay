<?php
session_start();
include "../connection.php";

// ✅ Add Senior PWD Service
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["add_service"])) {
    $resident_id = intval($_POST["resident_id"]);
    $service_type_id = intval($_POST["service_type_id"]);
    $service_date = $_POST["service_date"];

    $stmt = $con->prepare("INSERT INTO senior_pwd_services (resident_id, service_type_id, service_date) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $resident_id, $service_type_id, $service_date);

    if ($stmt->execute()) {
        $_SESSION["message"] = "Senior PWD service added successfully.";
    } else {
        $_SESSION["error"] = "Error adding service: " . $stmt->error;
    }

    $stmt->close();
    header("Location: senior_pwd.php");
    exit();
}

// ✅ Update Senior PWD Service
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update_service"])) {
    $service_id = intval($_POST["service_id"]);
    $resident_id = intval($_POST["resident_id"]);
    $service_type_id = intval($_POST["service_type_id"]);
    $service_date = $_POST["service_date"];

    $stmt = $con->prepare("UPDATE senior_pwd_services SET resident_id = ?, service_type_id = ?, service_date = ? WHERE service_id = ?");
    $stmt->bind_param("iisi", $resident_id, $service_type_id, $service_date, $service_id);

    if ($stmt->execute()) {
        $_SESSION["message"] = "Senior PWD service updated successfully.";
    } else {
        $_SESSION["error"] = "Error updating service: " . $stmt->error;
    }

    $stmt->close();
    header("Location: senior_pwd.php");
    exit();
}
?>
