<?php
session_start();
include "../connection.php";

// Add Document
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_document"])) {
    $document_type_id = intval($_POST["document_type_id"]); // Ensure it's an integer
    $resident_id = intval($_POST["resident_id"]); // Ensure it's an integer
    $file_path = ''; // Default file path (to be updated after file upload)

    // Handle file upload
    if (isset($_FILES['document_file']) && $_FILES['document_file']['error'] == 0) {
        $file_name = $_FILES['document_file']['name'];
        $file_tmp = $_FILES['document_file']['tmp_name'];
        $file_path = "uploads/" . basename($file_name); // Define the upload path

        if (move_uploaded_file($file_tmp, $file_path)) {
            // File uploaded successfully, insert record into the database
            $query = "INSERT INTO documents (document_type_id, resident_id, file_path) 
                      VALUES ('$document_type_id', '$resident_id', '$file_path')";
            
            if (mysqli_query($con, $query)) {
                $_SESSION["message"] = "Document added successfully.";
            } else {
                $_SESSION["error"] = "Error adding document: " . mysqli_error($con);
            }
        } else {
            $_SESSION["error"] = "Error uploading file.";
        }
    } else {
        $_SESSION["error"] = "No file selected or invalid file.";
    }

    header("Location: documents.php");
    exit();
}

// Update Document
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_document"])) {
    $document_id = intval($_POST["document_id"]);
    $document_type_id = intval($_POST["document_type_id"]);
    $resident_id = intval($_POST["resident_id"]);
    $file_path = ''; // Default file path (to be updated after file upload)

    // Handle file upload
    if (isset($_FILES['document_file']) && $_FILES['document_file']['error'] == 0) {
        $file_name = $_FILES['document_file']['name'];
        $file_tmp = $_FILES['document_file']['tmp_name'];
        $file_path = "uploads/" . basename($file_name); // Define the upload path

        if (move_uploaded_file($file_tmp, $file_path)) {
            // File uploaded successfully, update the document record
            $query = "UPDATE documents SET 
                      document_type_id='$document_type_id', 
                      resident_id='$resident_id', 
                      file_path='$file_path' 
                      WHERE document_id='$document_id'";
            
            if (mysqli_query($con, $query)) {
                $_SESSION["message"] = "Document updated successfully.";
            } else {
                $_SESSION["error"] = "Error updating document: " . mysqli_error($con);
            }
        } else {
            $_SESSION["error"] = "Error uploading file.";
        }
    } else {
        // If no new file is uploaded, update only other fields
        $query = "UPDATE documents SET 
                  document_type_id='$document_type_id', 
                  resident_id='$resident_id' 
                  WHERE document_id='$document_id'";

        if (mysqli_query($con, $query)) {
            $_SESSION["message"] = "Document updated successfully.";
        } else {
            $_SESSION["error"] = "Error updating document: " . mysqli_error($con);
        }
    }

    header("Location: documents.php");
    exit();
}

?>
