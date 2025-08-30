<?php
// Move autoloader and use statement to the top
require __DIR__ . '/../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

session_start();

if (!isset($_SESSION['role'])) {
    header("Location: ../../login.php");
    exit();
}

include "../connection.php";

// Configuration constants - FIXED: Added missing slash
define('UPLOAD_DIR', __DIR__ . '/image/');
define('MAX_FILE_SIZE', 5000000); // 5MB
define('MAX_ROWS', 1000);
define('ALLOWED_EXTENSIONS', ['csv', 'xlsx', 'xls']);
define('REQUIRED_HEADERS', ['fname', 'lname', 'bdate', 'gender']);
define('ALLOWED_IMAGE_EXTENSIONS', ['png', 'jpg', 'jpeg']);
define('MAX_IMAGE_SIZE', 2097152); // 2MB

// NEW: Function to get correct image path with fallback
function getImagePath($imageName) {
    if (empty($imageName) || $imageName === 'person-icon.png') {
        return 'image/person-icon.png';
    }
    
    $imagePath = 'image/' . basename($imageName);
    
    // Check if file exists, if not return default
    if (!file_exists($imagePath)) {
        return 'image/person-icon.png';
    }
    
    return $imagePath;
}

// NEW: Ensure image directory exists
if (!file_exists('image/')) {
    mkdir('image/', 0755, true);
}

// Handle AJAX upload requests FIRST - before any output
if (isset($_POST['ajax_upload']) && isset($_FILES['residents_file'])) {
    // Clear any existing output buffers
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    // Set JSON headers
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-cache, must-revalidate');
    
    $uploadResult = handleFileUpload($_FILES['residents_file'], $con);
    echo json_encode($uploadResult, JSON_UNESCAPED_UNICODE);
    exit();
}

// Handle debug endpoint
if (isset($_POST['debug_upload']) && isset($_POST['ajax_upload'])) {
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => true,
        'message' => 'Debug endpoint working correctly',
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    exit();
}

// Start output buffering for regular page requests
ob_start();
include('../head_css.php');

// Handle other form submissions
if (isset($_POST['btn_delete'])) {
    $deleted = false;
    $fk_error = false;
    foreach ($_POST['chk_delete'] ?? [] as $id) {
        // IMPROVED: Delete associated image file (except default)
        $stmt = $con->prepare("SELECT image FROM tblresident WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            if ($row['image'] !== 'person-icon.png' && file_exists(UPLOAD_DIR . $row['image'])) {
                @unlink(UPLOAD_DIR . $row['image']);
            }
        }
        $stmt->close();
        
        $stmt = $con->prepare("DELETE FROM tblresident WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $deleted = true;
        } else {
            $fk_error = true;
        }
        $stmt->close();
    }
    if ($deleted) $_SESSION['delete'] = 1;
    if ($fk_error) $_SESSION['fk_constraint'] = 1;
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

if (isset($_POST['btn_add'])) {
    $residentData = sanitizeResidentData($_POST);
    $residentData['image'] = 'person-icon.png';

    // IMPROVED: Handle image upload with better error handling
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, ALLOWED_IMAGE_EXTENSIONS)) {
            $_SESSION['add_error'] = 'Invalid image format. Only PNG, JPG, or JPEG allowed.';
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
        if ($_FILES['image']['size'] > MAX_IMAGE_SIZE) {
            $_SESSION['add_error'] = 'Image size too large. Maximum size is 2MB.';
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
        
        // Create upload directory if it doesn't exist
        if (!file_exists(UPLOAD_DIR)) {
            mkdir(UPLOAD_DIR, 0755, true);
        }
        
        $fileName = uniqid('image_', true) . '.' . $extension;
        $fileDestination = UPLOAD_DIR . $fileName;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $fileDestination)) {
            $residentData['image'] = $fileName; // Store only filename
        } else {
            $_SESSION['add_error'] = 'Failed to upload image.';
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
    }

    // Check for duplicates
    $stmt = $con->prepare("SELECT id FROM tblresident WHERE fname = ? AND lname = ? AND bdate = ?");
    $stmt->bind_param("sss", $residentData['fname'], $residentData['lname'], $residentData['bdate']);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $_SESSION['duplicate_error'] = 1;
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
    $stmt->close();

    // Insert resident
    if (insertSingleResident($con, $residentData)) {
        $_SESSION['added'] = 1;
    } else {
        $_SESSION['add_error'] = 'Failed to add resident.';
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
} 
if (isset($_POST['btn_edit'])) {
    $id = (int)$_POST['btn_edit'];
    $residentData = sanitizeResidentData($_POST);
    
    // Get current image from database
    $stmt = $con->prepare("SELECT image FROM tblresident WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $currentData = $result->fetch_assoc();
    $stmt->close();
    
    $residentData['image'] = $currentData['image'] ?? 'person-icon.png'; // Keep current image by default

    // Handle new image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, ALLOWED_IMAGE_EXTENSIONS)) {
            $_SESSION['edit_error'] = 'Invalid image format. Only PNG, JPG, or JPEG allowed.';
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
        if ($_FILES['image']['size'] > MAX_IMAGE_SIZE) {
            $_SESSION['edit_error'] = 'Image size too large. Maximum size is 2MB.';
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
        
        // Create upload directory if it doesn't exist
        if (!file_exists(UPLOAD_DIR)) {
            mkdir(UPLOAD_DIR, 0755, true);
        }
        
        $fileName = uniqid('image_', true) . '.' . $extension;
        $fileDestination = UPLOAD_DIR . $fileName;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $fileDestination)) {
            // Delete old image if it's not the default and exists
            if ($residentData['image'] !== 'person-icon.png' && !empty($residentData['image']) && file_exists(UPLOAD_DIR . $residentData['image'])) {
                @unlink(UPLOAD_DIR . $residentData['image']);
            }
            $residentData['image'] = $fileName; // Update to new image
        } else {
            $_SESSION['edit_error'] = 'Failed to upload image.';
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
    }
    // If no new image is uploaded, retain the existing image (already set above)

    // Check for duplicates excluding self
    $stmt = $con->prepare("SELECT id FROM tblresident WHERE fname = ? AND lname = ? AND bdate = ? AND id != ?");
    $stmt->bind_param("sssi", $residentData['fname'], $residentData['lname'], $residentData['bdate'], $id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $_SESSION['duplicate_error'] = 1;
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
    $stmt->close();

    // Update resident
    if (updateResident($con, $id, $residentData)) {
        $_SESSION['edit'] = 1;
    } else {
        $_SESSION['edit_error'] = 'Failed to update resident: ' . $con->error;
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

/**
 * Format full name with proper capitalization
 */
function formatFullName($fname, $mname, $lname) {
    $fullName = trim($fname . ' ' . $mname . ' ' . $lname);
    // Remove extra spaces
    $fullName = preg_replace('/\s+/', ' ', $fullName);
    return $fullName;
}

/**
 * Handle file upload and processing
 */
function handleFileUpload($file, $con) {
    try {
        // Create upload directory if needed
        if (!file_exists(UPLOAD_DIR) && !mkdir(UPLOAD_DIR, 0755, true)) {
            return ['success' => false, 'message' => 'Failed to create uploads directory.'];
        }

        // Validate file upload
        $validation = validateFileUpload($file);
        if (!$validation['success']) {
            return $validation;
        }

        // Move uploaded file to temporary location
        $tempFile = UPLOAD_DIR . uniqid('temp_', true) . '.' . $validation['extension'];
        if (!move_uploaded_file($file['tmp_name'], $tempFile)) {
            return ['success' => false, 'message' => 'Failed to upload file.'];
        }

        // Process the file
        $result = processUploadedFile($tempFile, $con);
        
        // Clean up temp file
        @unlink($tempFile);
        
        return $result;
        
    } catch (Exception $e) {
        error_log("Upload error: " . $e->getMessage());
        return ['success' => false, 'message' => 'Upload failed: ' . $e->getMessage()];
    }
}

/**
 * Validate file upload
 */
function validateFileUpload($file) {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'File upload error occurred.'];
    }

    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, ALLOWED_EXTENSIONS)) {
        return ['success' => false, 'message' => 'Invalid file type. Only CSV, XLSX, and XLS files are allowed.'];
    }

    if ($file['size'] > MAX_FILE_SIZE) {
        return ['success' => false, 'message' => 'File size too large. Maximum size is 5MB.'];
    }

    if ($file['size'] == 0) {
        return ['success' => false, 'message' => 'Uploaded file is empty.'];
    }

    return ['success' => true, 'extension' => $extension];
}

/**
 * Process uploaded file
 */
function processUploadedFile($filePath, $con) {
    $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
    
    try {
        if ($extension === 'csv') {
            $data = parseCSVFile($filePath);
        } else {
            $data = parseExcelFile($filePath);
        }
        
        if (!$data['success']) {
            return $data;
        }

        return insertResidentData($data['data'], $con);
        
    } catch (Exception $e) {
        error_log("File processing error: " . $e->getMessage());
        return ['success' => false, 'message' => 'Error processing file: ' . $e->getMessage()];
    }
}

/**
 * Parse CSV file
 */
function parseCSVFile($filePath) {
    $data = [];
    
    $handle = fopen($filePath, "r");
    if ($handle === FALSE) {
        return ['success' => false, 'message' => 'Unable to open CSV file.'];
    }

    // Get headers
    $headers = fgetcsv($handle, 0, ",");
    if ($headers === FALSE) {
        fclose($handle);
        return ['success' => false, 'message' => 'Unable to read CSV headers.'];
    }

    // Normalize headers
    $headers = array_map(function($h) { return strtolower(trim($h)); }, $headers);
    
    // Validate headers
    foreach (REQUIRED_HEADERS as $required) {
        if (!in_array(strtolower($required), $headers)) {
            fclose($handle);
            return ['success' => false, 'message' => "Missing required header: $required"];
        }
    }

    // Process data rows
    $rowNumber = 2;
    while (($row = fgetcsv($handle, 0, ",")) !== FALSE && count($data) < MAX_ROWS) {
        if (count($row) >= count($headers) && array_filter($row, 'trim')) {
            $rowData = array_combine($headers, array_slice($row, 0, count($headers)));
            if ($rowData !== false) {
                $data[] = $rowData;
            }
        }
        $rowNumber++;
    }
    
    fclose($handle);
    return ['success' => true, 'data' => $data];
}

/**
 * Parse Excel file
 */
function parseExcelFile($filePath) {
    try {
        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray(null, true, true, true);
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error reading Excel file: ' . $e->getMessage()];
    }
    
    if (empty($rows)) {
        return ['success' => false, 'message' => 'Excel file is empty.'];
    }

    // Get headers
    $firstRow = array_shift($rows);
    $headers = array_map(function($h) { return strtolower(trim($h)); }, $firstRow);
    
    // Validate headers
    foreach (REQUIRED_HEADERS as $required) {
        if (!in_array(strtolower($required), $headers)) {
            return ['success' => false, 'message' => "Missing required header: $required"];
        }
    }

    // Process data rows
    $data = [];
    foreach ($rows as $row) {
        if (count($data) >= MAX_ROWS) break;
        
        if (array_filter($row, 'trim')) {
            $processedRow = [];
            foreach ($headers as $index => $header) {
                $value = isset($row[array_keys($row)[$index]]) ? $row[array_keys($row)[$index]] : '';
                
                // Handle Excel date format for birthdate
                if ($header === 'bdate' && is_numeric($value) && $value > 0) {
                    try {
                        $value = Date::excelToDateTimeObject($value)->format('Y-m-d');
                    } catch (Exception $e) {
                        // Keep original value if conversion fails
                    }
                }
                
                $processedRow[$header] = $value;
            }
            $data[] = $processedRow;
        }
    }
    
    return ['success' => true, 'data' => $data];
}

/**
 * Insert resident data into database
 */
function insertResidentData($uploadedData, $con) {
    if (empty($uploadedData)) {
        return ['success' => false, 'message' => 'No valid data found in the uploaded file.'];
    }
    
    $successCount = 0;
    $errorCount = 0;
    $errors = [];
    $skippedCount = 0;

    // Start transaction
    $con->begin_transaction();

    try {
        foreach ($uploadedData as $rowIndex => $row) {
            // Validate required fields
            $missingFields = [];
            foreach (REQUIRED_HEADERS as $field) {
                if (empty(trim($row[strtolower($field)] ?? ''))) {
                    $missingFields[] = $field;
                }
            }

            if (!empty($missingFields)) {
                $errorCount++;
                $errors[] = "Row " . ($rowIndex + 2) . ": Missing required fields: " . implode(', ', $missingFields);
                continue;
            }

            // Sanitize data
            $residentData = sanitizeResidentData($row);
            
            // Validate and convert date
            $convertedDate = convertDateFormat($residentData['bdate']);
            if (!$convertedDate) {
                $errorCount++;
                $errors[] = "Row " . ($rowIndex + 2) . ": Invalid birth date format: " . $residentData['bdate'];
                continue;
            }
            $residentData['bdate'] = $convertedDate;

            // Check for duplicates
            $stmt = $con->prepare("SELECT id FROM tblresident WHERE fname = ? AND lname = ? AND bdate = ?");
            $stmt->bind_param("sss", $residentData['fname'], $residentData['lname'], $residentData['bdate']);
            $stmt->execute();
            
            if ($stmt->get_result()->num_rows > 0) {
                $skippedCount++;
                $errors[] = "Row " . ($rowIndex + 2) . ": Duplicate resident - {$residentData['fname']} {$residentData['lname']}";
                $stmt->close();
                continue;
            }
            $stmt->close();

            // Insert resident
            if (insertSingleResident($con, $residentData)) {
                $successCount++;
            } else {
                $errorCount++;
                $errors[] = "Row " . ($rowIndex + 2) . ": Database insertion failed";
            }
        }

        // Commit transaction
        $con->commit();
        
    } catch (Exception $e) {
        $con->rollback();
        error_log("Transaction error: " . $e->getMessage());
        return ['success' => false, 'message' => 'Database transaction failed: ' . $e->getMessage()];
    }

    // Format result message
    $message = "";
    if ($successCount > 0) {
        $message = "Successfully uploaded $successCount residents.";
        if ($skippedCount > 0) {
            $message .= " Skipped $skippedCount duplicates.";
        }
        if ($errorCount > 0) {
            $message .= " $errorCount errors occurred.";
        }
    } else {
        $message = "Upload failed: No residents were added.";
    }
    
    if (!empty($errors) && count($errors) <= 10) {
        $message .= "<br><br>Details:<br>" . implode("<br>", $errors);
    } elseif (!empty($errors)) {
        $message .= "<br><br>First 10 errors:<br>" . implode("<br>", array_slice($errors, 0, 10));
        $message .= "<br>... and " . (count($errors) - 10) . " more errors.";
    }
    
    return [
        'success' => $successCount > 0,
        'message' => $message,
        'stats' => [
            'successful' => $successCount,
            'errors' => $errorCount,
            'skipped' => $skippedCount
        ]
    ];
}

/**
 * Insert single resident
 */
function insertSingleResident($con, $residentData) {
    $sql = "INSERT INTO tblresident (fname, lname, mname, bdate, bplace, age, contact_no, civilstatus, occupation, religion, gender, pwd, senior_citizen, sitio, purok, address, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $con->prepare($sql);
    if (!$stmt) {
        error_log("Prepare failed: " . $con->error);
        return false;
    }
    
    $result = $stmt->bind_param("sssssssssssssssss", 
        $residentData['fname'], 
        $residentData['lname'], 
        $residentData['mname'], 
        $residentData['bdate'], 
        $residentData['bplace'], 
        $residentData['age'],
        $residentData['contact_no'], 
        $residentData['civilstatus'], 
        $residentData['occupation'], 
        $residentData['religion'], 
        $residentData['gender'], 
        $residentData['pwd'], 
        $residentData['senior_citizen'], 
        $residentData['sitio'], 
        $residentData['purok'], 
        $residentData['address'],
        $residentData['image']
    );
    
    if (!$result) {
        error_log("Bind failed: " . $stmt->error);
        $stmt->close();
        return false;
    }
    
    $result = $stmt->execute();
    if (!$result) {
        error_log("Execute failed: " . $stmt->error);
        $stmt->close();
        return false;
    }
    
    $stmt->close();
    return true;
}

/**
 * Update resident
 */
function updateResident($con, $id, $residentData) {
    $sql = "UPDATE tblresident SET fname=?, lname=?, mname=?, bdate=?, bplace=?, age=?, contact_no=?, civilstatus=?, occupation=?, religion=?, gender=?, pwd=?, senior_citizen=?, sitio=?, purok=?, address=?, image=? WHERE id=?";
    
    $stmt = $con->prepare($sql);
    if (!$stmt) return false;
    
    $stmt->bind_param("sssssssssssssssssi", 
        $residentData['fname'], $residentData['lname'], $residentData['mname'], $residentData['bdate'], 
        $residentData['bplace'], $residentData['age'], $residentData['contact_no'], $residentData['civilstatus'], 
        $residentData['occupation'], $residentData['religion'], $residentData['gender'], $residentData['pwd'], 
        $residentData['senior_citizen'], $residentData['sitio'], $residentData['purok'], $residentData['address'], 
        $residentData['image'], $id
    );
    
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

/**
 * Sanitize resident data
 */
function sanitizeResidentData($row) {
    // Handle different case variations of keys
    $getValue = function($key) use ($row) {
        $lowerKey = strtolower($key);
        foreach ($row as $k => $v) {
            if (strtolower($k) === $lowerKey) {
                return trim($v);
            }
        }
        return '';
    };

    $data = [
        'fname' => $getValue('fname'),
        'lname' => $getValue('lname'),
        'mname' => $getValue('mname') ?: 'N/A',
        'bdate' => $getValue('bdate'),
        'bplace' => $getValue('bplace') ?: 'N/A',
        'age' => '0',
        'contact_no' => $getValue('contact_no') ?: 'N/A',
        'civilstatus' => $getValue('civilstatus') ?: 'Single',
        'occupation' => $getValue('occupation') ?: 'N/A',
        'religion' => $getValue('religion') ?: 'N/A',
        'gender' => $getValue('gender'),
        'pwd' => $getValue('pwd') ?: 'No',
        'senior_citizen' => $getValue('senior_citizen') ?: 'No',
        'sitio' => $getValue('sitio'),
        'purok' => $getValue('purok'),
        'address' => $getValue('address'),
        'image' => 'person-icon.png'
    ];

    // Calculate age from birthdate or use provided age
    $inputAge = (int)$getValue('age');
    if ($inputAge > 0 && $inputAge <= 150) {
        $data['age'] = (string)$inputAge;
    } elseif (!empty($data['bdate'])) {
        $calculatedAge = calculateAge($data['bdate']);
        $data['age'] = (string)max(0, $calculatedAge);
    }

    // Normalize gender
    $gender = strtolower($data['gender']);
    if (in_array($gender, ['male', 'm'])) {
        $data['gender'] = 'Male';
    } elseif (in_array($gender, ['female', 'f'])) {
        $data['gender'] = 'Female';
    }

    // Normalize Yes/No fields
    foreach (['pwd', 'senior_citizen'] as $field) {
        $value = strtolower($data[$field]);
        if (in_array($value, ['yes', 'y', '1', 'true'])) {
            $data[$field] = 'Yes';
        } else {
            $data[$field] = 'No';
        }
    }

    return $data;
}

/**
 * Convert date format to Y-m-d
 */
function convertDateFormat($date) {
    if (empty($date)) return false;
    
    $formats = [
        'Y-m-d', 'Y/m/d', 'm/d/Y', 'd/m/Y', 'm-d-Y', 'd-m-Y'
    ];
    
    foreach ($formats as $format) {
        $d = DateTime::createFromFormat($format, $date);
        if ($d && $d->format($format) === $date) {
            return $d->format('Y-m-d');
        }
    }
    
    return false;
}

/**
 * Calculate age from birthdate
 */
function calculateAge($birthdate) {
    try {
        $birthDate = new DateTime($birthdate);
        $currentDate = new DateTime();
        return $currentDate->diff($birthDate)->y;
    } catch (Exception $e) {
        return 0;
    }
}

?>

<!DOCTYPE html>
<html>
<?php include('../head_css.php'); ?>

<style>
.upload-area {
    border: 2px dashed #ccc;
    border-radius: 8px;
    padding: 30px;
    text-align: center;
    margin: 20px 0;
    background-color: #f9f9f9;
    transition: all 0.3s ease;
}
.upload-area:hover {
    border-color: #007bff;
    background-color: #f0f8ff;
}
.upload-area.drag-over {
    border-color: #007bff;
    background-color: #e3f2fd;
}
.upload-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.7);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}
.upload-spinner {
    background: white;
    padding: 30px;
    border-radius: 8px;
    text-align: center;
}
.full-name-cell {
    font-weight: 500;
}

/* IMPROVED: Better image styling with fallback handling */
.img-circle {
    object-fit: cover;
    border: 2px solid #ddd;
    background-color: #f5f5f5;
}

/* Fallback for broken images */
img[src=""], img:not([src]) {
    opacity: 0;
}

/* Style for default image */
img[src*="person-icon.png"] {
    background-color: #f5f5f5;
    border-color: #ccc;
}

.resident-image {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 50%;
    border: 2px solid #ddd;
    background-color: #f5f5f5;
}

.profile-image-preview {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 50%;
    border: 2px solid #ddd;
    background-color: #f5f5f5;
}
</style>

<body class="skin-black">
    <?php include('../header.php'); ?>

    <div class="wrapper row-offcanvas row-offcanvas-left">
        <?php include('../sidebar-left.php'); ?>

        <aside class="right-side">
            <section class="content-header">
                <h1>Resident Management</h1>
            </section>

            <section class="content">
                <div class="row">
                    <div class="box">
                        <div class="box-header">
                            <div style="padding:10px;">
                                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addResidentModal">
                                    <i class="fa fa-user-plus"></i> Add Resident
                                </button>  
                                
                                <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#uploadResidentModal">
                                    <i class="fa fa-upload"></i> Upload Residents
                                </button>
                                
                                <a href="download_template.php" class="btn btn-info btn-sm">
                                    <i class="fa fa-download"></i> Download Template
                                </a>
                                
                                <?php if (!isset($_SESSION['staff'])) { ?>
                                    <button class="btn btn-danger btn-sm" id="deleteSelectedBtn">
                                        <i class="fas fa-trash-alt"></i> Delete Selected
                                    </button> 
                                <?php } ?>

                                <div class="pull-right" style="display: inline-block;">
                                    <select id="filter_pwd" class="form-control" style="width: 150px; display: inline-block; margin-right: 10px;">
                                        <option value="">All PWD</option>
                                        <option value="Yes">PWD</option>
                                        <option value="No">Non-PWD</option>
                                    </select>

                                    <select id="filter_senior" class="form-control" style="width: 180px; display: inline-block; margin-right: 10px;">
                                        <option value="">All Senior Citizens</option>
                                        <option value="Yes">Senior Citizen</option>
                                        <option value="No">Non-Senior Citizen</option>
                                    </select>

                                    <select id="filter_gender" class="form-control" style="width: 120px; display: inline-block;">
                                        <option value="">All Gender</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                    </select>
                                </div>
                            </div>    
                                                       
                        </div>
<hr>
                        <div class="box-body table-responsive">
                            <form method="post" id="deleteForm">
                                <table id="resident_table" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <?php if (!isset($_SESSION['staff'])) { ?>
                                                <th style="width: 20px !important;">
                                                    <input type="checkbox" name="chk_delete[]" class="cbxMain" onchange="checkMain(this)" />
                                                </th>
                                            <?php } ?>
                                            <th>Image</th>
                                            <th>Full Name</th>
                                            <th>Birthdate</th>
                                            <th>Birthplace</th>
                                            <th>Age</th>
                                            <th>Contact No</th>
                                            <th>Civil Status</th>
                                            <th>Occupation</th>
                                            <th>Religion</th>
                                            <th>Gender</th>
                                            <th>PWD</th>
                                            <th>Senior Citizen</th>
                                            <th>Sitio</th>
                                            <th>Purok</th>
                                            <th>Address</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $query = mysqli_query($con, "SELECT id, lname, fname, mname, bdate, bplace, age, contact_no, civilstatus, occupation, religion, gender, pwd, senior_citizen, sitio, purok, address, image FROM tblresident ORDER BY lname, fname");

                                        while ($row = mysqli_fetch_assoc($query)) {
                                            $fullName = formatFullName($row['fname'], $row['mname'], $row['lname']);
                                            // IMPROVED: Use new getImagePath function with proper fallback
                                            $imageSrc = getImagePath($row['image']);
                                            
                                            echo '<tr>';
                                            if (!isset($_SESSION['staff'])) {
                                                echo '<td><input type="checkbox" name="chk_delete[]" class="chk_delete" value="' . $row['id'] . '" /></td>';
                                            }
                                            // IMPROVED: Better image display with error handling
                                            echo '<td><img src="' . htmlspecialchars($imageSrc) . '" class="resident-image" alt="Resident Image" onerror="this.src=\'image/person-icon.png\'; this.onerror=null;"/></td>';
                                            echo '<td class="full-name-cell">' . htmlspecialchars($fullName) . '</td>';
                                            echo '<td>' . htmlspecialchars($row['bdate']) . '</td>';
                                            echo '<td>' . htmlspecialchars($row['bplace']) . '</td>';
                                            echo '<td>' . $row['age'] . '</td>';
                                            echo '<td>' . htmlspecialchars($row['contact_no']) . '</td>';
                                            echo '<td>' . htmlspecialchars($row['civilstatus']) . '</td>';
                                            echo '<td>' . htmlspecialchars($row['occupation']) . '</td>';
                                            echo '<td>' . htmlspecialchars($row['religion']) . '</td>';
                                            echo '<td>' . htmlspecialchars($row['gender']) . '</td>';
                                            echo '<td><span class="label label-' . ($row['pwd'] === 'Yes' ? 'warning' : 'default') . '">' . htmlspecialchars($row['pwd']) . '</span></td>';
                                            echo '<td><span class="label label-' . ($row['senior_citizen'] === 'Yes' ? 'info' : 'default') . '">' . htmlspecialchars($row['senior_citizen']) . '</span></td>';
                                            echo '<td>' . htmlspecialchars($row['sitio']) . '</td>';
                                            echo '<td>' . htmlspecialchars($row['purok']) . '</td>';
                                            echo '<td>' . htmlspecialchars($row['address']) . '</td>';
                                            echo '<td>
                                                <button class="btn btn-primary btn-sm" data-target="#editModal' . $row['id'] . '" data-toggle="modal">
                                                    <i class="fa fa-pencil-square-o"></i> Edit
                                                </button>
                                            </td>';
                                            echo '</tr>';

                                            // Edit Modal for each resident
                                            include_edit_modal($row);
                                        }

                                        // FIXED: Updated edit modal generation function
                                        function include_edit_modal($row) {
                                            // Fetch unique sitio and purok options
                                            $sitioQuery = mysqli_query($GLOBALS['con'], "SELECT DISTINCT sitio FROM sitio_purok WHERE sitio IS NOT NULL AND sitio != '' ORDER BY sitio");
                                            $purokQuery = mysqli_query($GLOBALS['con'], "SELECT DISTINCT purok FROM sitio_purok WHERE purok IS NOT NULL AND purok != '' ORDER BY purok");
                                            $sitios = [];
                                            $puroks = [];
                                            while ($sitio = mysqli_fetch_assoc($sitioQuery)) {
                                                $sitios[] = $sitio['sitio'];
                                            }
                                            while ($purok = mysqli_fetch_assoc($purokQuery)) {
                                                $puroks[] = $purok['purok'];
                                            }
                                            
                                            // Get proper image path for edit modal
                                            $imageSrc = getImagePath($row['image']);
                                            ?>
                                            <div class="modal fade" id="editModal<?php echo $row['id']; ?>" tabindex="-1" role="dialog">
                                                <div class="modal-dialog modal-lg" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                            <h4 class="modal-title">Edit Resident - <?php echo htmlspecialchars($row['fname'] . ' ' . $row['lname']); ?></h4>
                                                        </div>
                                                        <form method="post" enctype="multipart/form-data">
                                                            <div class="modal-body">
                                                                <!-- REMOVED: old_image hidden field - we'll get it from database instead -->
                                                                
                                                                <div class="row">
                                                                    <div class="col-md-4">
                                                                        <div class="form-group">
                                                                            <label>First Name *</label>
                                                                            <input type="text" name="fname" class="form-control" value="<?php echo htmlspecialchars($row['fname']); ?>" required>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <div class="form-group">
                                                                            <label>Last Name *</label>
                                                                            <input type="text" name="lname" class="form-control" value="<?php echo htmlspecialchars($row['lname']); ?>" required>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <div class="form-group">
                                                                            <label>Middle Name</label>
                                                                            <input type="text" name="mname" class="form-control" value="<?php echo htmlspecialchars($row['mname']); ?>">
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="row">
                                                                    <div class="col-md-4">
                                                                        <div class="form-group">
                                                                            <label>Birth Date *</label>
                                                                            <input type="date" name="bdate" class="form-control" value="<?php echo htmlspecialchars($row['bdate']); ?>" required>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <div class="form-group">
                                                                            <label>Age</label>
                                                                            <input type="number" name="age" class="form-control" value="<?php echo htmlspecialchars($row['age']); ?>" min="0" max="150">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <div class="form-group">
                                                                            <label>Gender *</label>
                                                                            <select name="gender" class="form-control" required>
                                                                                <option value="Male" <?php if ($row['gender'] == 'Male') echo 'selected'; ?>>Male</option>
                                                                                <option value="Female" <?php if ($row['gender'] == 'Female') echo 'selected'; ?>>Female</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label>Birth Place</label>
                                                                            <input type="text" name="bplace" class="form-control" value="<?php echo htmlspecialchars($row['bplace']); ?>">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label>Contact Number</label>
                                                                            <input type="text" name="contact_no" class="form-control" value="<?php echo htmlspecialchars($row['contact_no']); ?>">
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="row">
                                                                    <div class="col-md-4">
                                                                        <div class="form-group">
                                                                            <label>Civil Status</label>
                                                                            <select name="civilstatus" class="form-control">
                                                                                <option value="Single" <?php if ($row['civilstatus'] == 'Single') echo 'selected'; ?>>Single</option>
                                                                                <option value="Married" <?php if ($row['civilstatus'] == 'Married') echo 'selected'; ?>>Married</option>
                                                                                <option value="Widowed" <?php if ($row['civilstatus'] == 'Widowed') echo 'selected'; ?>>Widowed</option>
                                                                                <option value="Divorced" <?php if ($row['civilstatus'] == 'Divorced') echo 'selected'; ?>>Divorced</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <div class="form-group">
                                                                            <label>Occupation</label>
                                                                            <input type="text" name="occupation" class="form-control" value="<?php echo htmlspecialchars($row['occupation']); ?>">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <div class="form-group">
                                                                            <label>Religion</label>
                                                                            <input type="text" name="religion" class="form-control" value="<?php echo htmlspecialchars($row['religion']); ?>">
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="row">
                                                                    <div class="col-md-3">
                                                                        <div class="form-group">
                                                                            <label>PWD</label>
                                                                            <select name="pwd" class="form-control">
                                                                                <option value="No" <?php if ($row['pwd'] == 'No') echo 'selected'; ?>>No</option>
                                                                                <option value="Yes" <?php if ($row['pwd'] == 'Yes') echo 'selected'; ?>>Yes</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <div class="form-group">
                                                                            <label>Senior Citizen</label>
                                                                            <select name="senior_citizen" class="form-control">
                                                                                <option value="No" <?php if ($row['senior_citizen'] == 'No') echo 'selected'; ?>>No</option>
                                                                                <option value="Yes" <?php if ($row['senior_citizen'] == 'Yes') echo 'selected'; ?>>Yes</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <div class="form-group">
                                                                            <label>Sitio *</label>
                                                                            <select name="sitio" class="form-control" required>
                                                                                <option value="">Select Sitio</option>
                                                                                <?php
                                                                                foreach ($sitios as $sitio) {
                                                                                    echo '<option value="' . htmlspecialchars($sitio) . '"';
                                                                                    if ($row['sitio'] == $sitio) echo ' selected';
                                                                                    echo '>' . htmlspecialchars($sitio) . '</option>';
                                                                                }
                                                                                ?>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <div class="form-group">
                                                                            <label>Purok *</label>
                                                                            <select name="purok" class="form-control" required>
                                                                                <option value="">Select Purok</option>
                                                                                <?php
                                                                                foreach ($puroks as $purok) {
                                                                                    echo '<option value="' . htmlspecialchars($purok) . '"';
                                                                                    if ($row['purok'] == $purok) echo ' selected';
                                                                                    echo '>' . htmlspecialchars($purok) . '</option>';
                                                                                }
                                                                                ?>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="row">
                                                                    <div class="col-md-8">
                                                                        <div class="form-group">
                                                                            <label>Address</label>
                                                                            <textarea name="address" class="form-control" rows="2"><?php echo htmlspecialchars($row['address']); ?></textarea>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <div class="form-group">
                                                                            <label>Profile Image</label>
                                                                            <div class="mb-2">
                                                                                <img src="<?php echo htmlspecialchars($imageSrc); ?>" 
                                                                                     class="profile-image-preview mb-2" 
                                                                                     alt="Current Profile Image"
                                                                                     id="preview_<?php echo $row['id']; ?>"
                                                                                     onerror="this.src='image/person-icon.png'; this.onerror=null;">
                                                                            </div>
                                                                            <input type="file" name="image" class="form-control" accept="image/*" 
                                                                                   onchange="previewImage(this, 'preview_<?php echo $row['id']; ?>')">
                                                                            <small class="text-muted">Optional: PNG, JPG, or JPEG (max 2MB)</small>
                                                                            <div style="margin-top: 5px;">
                                                                                <small class="text-info">Current: <?php echo htmlspecialchars($row['image']); ?></small>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                                                <button type="submit" name="btn_edit" value="<?php echo $row['id']; ?>" class="btn btn-primary">
                                                                    <i class="fa fa-save"></i> Update Resident
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </form>
                        </div>
                    </div>

                    <!-- Upload Modal -->
                    <div class="modal fade" id="uploadResidentModal" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h4><i class="fa fa-upload"></i> Upload Residents Data</h4>
                                </div>
                                <form id="uploadForm">
                                    <div class="modal-body">
                                        <div class="alert alert-info">
                                            <strong>Instructions:</strong>
                                            <ul>
                                                <li>Download the template file first</li>
                                                <li>Required fields: First Name, Last Name, Birth Date, Gender</li>
                                                <li>Date format: YYYY-MM-DD, MM/DD/YYYY, or DD/MM/YYYY</li>
                                                <li>Supported formats: CSV, XLSX, XLS (Max 5MB)</li>
                                                <li>Maximum 1000 residents per upload</li>
                                            </ul>
                                        </div>

                                        <div class="upload-area" id="uploadArea">
                                            <i class="fa fa-cloud-upload fa-3x" style="color: #ccc; margin-bottom: 15px;"></i>
                                            <h4>Drag and drop your file here</h4>
                                            <p>or</p>
                                            <button type="button" class="btn btn-primary" onclick="document.getElementById('fileInput').click()">
                                                <i class="fa fa-folder-open"></i> Choose File
                                            </button>
                                            <input type="file" id="fileInput" name="residents_file" style="display: none;" 
                                                   accept=".xlsx,.xls,.csv" onchange="handleFileSelect(this)">
                                        </div>

                                        <div id="fileInfo" style="display: none;">
                                            <div class="alert alert-success">
                                                <i class="fa fa-file-excel-o"></i> 
                                                <span id="fileName"></span>
                                                <button type="button" class="btn btn-xs btn-danger pull-right" onclick="clearFile()">
                                                    <i class="fa fa-times"></i> Remove
                                                </button>
                                            </div>
                                        </div>

                                        <div style="margin-top: 15px;">
                                            <small>
                                                <a href="download_template.php" target="_blank" class="btn btn-link btn-xs">
                                                    <i class="fa fa-download"></i> Download Template
                                                </a>
                                            </small>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-success" id="uploadBtn" disabled>
                                            <i class="fa fa-upload"></i> Upload Data
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

<!-- Add Resident Modal -->
<div class="modal fade" id="addResidentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4>Add Resident</h4>
            </div>
            <form method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>First Name *</label>
                                <input type="text" name="fname" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Last Name *</label>
                                <input type="text" name="lname" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Middle Name</label>
                                <input type="text" name="mname" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Gender *</label>
                                <select name="gender" class="form-control" required>
                                    <option value="">Select Gender</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Birth Date *</label>
                                <input type="date" name="bdate" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Age</label>
                                <input type="number" name="age" class="form-control" min="0" max="150">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Birth Place</label>
                                <input type="text" name="bplace" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Contact Number</label>
                                <input type="text" name="contact_no" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Civil Status</label>
                                <select name="civilstatus" class="form-control">
                                    <option value="Single">Single</option>
                                    <option value="Married">Married</option>
                                    <option value="Widowed">Widowed</option>
                                    <option value="Divorced">Divorced</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Occupation</label>
                                <input type="text" name="occupation" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Religion</label>
                                <input type="text" name="religion" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>PWD</label>
                                <select name="pwd" class="form-control">
                                    <option value="No">No</option>
                                    <option value="Yes">Yes</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Senior Citizen</label>
                                <select name="senior_citizen" class="form-control">
                                    <option value="No">No</option>
                                    <option value="Yes">Yes</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Sitio *</label>
                                <select name="sitio" class="form-control" required>
                                    <option value="">Select Sitio</option>
                                    <?php
                                    $sitioQuery = mysqli_query($con, "SELECT DISTINCT sitio FROM sitio_purok WHERE sitio IS NOT NULL AND sitio != '' ORDER BY sitio");
                                    while ($sitio = mysqli_fetch_assoc($sitioQuery)) {
                                        echo '<option value="' . htmlspecialchars($sitio['sitio']) . '">' . htmlspecialchars($sitio['sitio']) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Purok *</label>
                                <select name="purok" class="form-control" required>
                                    <option value="">Select Purok</option>
                                    <?php
                                    $purokQuery = mysqli_query($con, "SELECT DISTINCT purok FROM sitio_purok WHERE purok IS NOT NULL AND purok != '' ORDER BY purok");
                                    while ($purok = mysqli_fetch_assoc($purokQuery)) {
                                        echo '<option value="' . htmlspecialchars($purok['purok']) . '">' . htmlspecialchars($purok['purok']) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Profile Image</label>
                                <input type="file" name="image" class="form-control" accept="image/*">
                                <small class="text-muted">Optional: PNG, JPG, or JPEG (max 2MB)</small>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Address</label>
                        <textarea name="address" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" name="btn_add" class="btn btn-primary">Add Resident</button>
                </div>
            </form>
        </div>
    </div>
</div>

                    <!-- Upload Overlay -->
                    <div class="upload-overlay" id="uploadOverlay">
                        <div class="upload-spinner">
                            <i class="fa fa-spinner fa-spin fa-3x"></i>
                            <div style="margin-top: 15px; font-size: 16px;">Uploading residents data...</div>
                            <div style="margin-top: 10px; color: #666;">Please wait while we process your file.</div>
                        </div>
                    </div>

                </div>
            </section>
        </aside>
    </div>

    <?php include "../footer.php"; ?>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // FIXED: Image preview function for edit modals
        function previewImage(input, previewId) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById(previewId).src = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        $(document).ready(function() {
            // Initialize DataTable
            var table = $("#resident_table").DataTable({
                "responsive": true,
                "pageLength": 25,
                "order": [[<?php echo !isset($_SESSION['staff']) ? '2' : '1'; ?>, 'asc']],
                "columnDefs": [
                    { "orderable": false, "targets": [<?php echo !isset($_SESSION['staff']) ? '0,16' : '15'; ?>] }
                ]
            });

            // Define column indices based on user role
            var columnIndices = {
                gender: <?php echo !isset($_SESSION['staff']) ? '10' : '9'; ?>,
                pwd: <?php echo !isset($_SESSION['staff']) ? '11' : '10'; ?>,
                senior: <?php echo !isset($_SESSION['staff']) ? '12' : '11'; ?>
            };

            // Custom search function for multiple filters
            $.fn.dataTable.ext.search.push(
                function(settings, data, dataIndex) {
                    var pwdFilter = $('#filter_pwd').val();
                    var seniorFilter = $('#filter_senior').val();
                    var genderFilter = $('#filter_gender').val();

                    var pwdValue = data[columnIndices.pwd].replace(/<[^>]+>/g, '').trim(); // Remove HTML tags (e.g., from labels)
                    var seniorValue = data[columnIndices.senior].replace(/<[^>]+>/g, '').trim();
                    var genderValue = data[columnIndices.gender].trim();

                    // Apply PWD filter
                    if (pwdFilter && pwdValue !== pwdFilter) {
                        return false;
                    }

                    // Apply Senior Citizen filter
                    if (seniorFilter && seniorValue !== seniorFilter) {
                        return false;
                    }

                    // Apply Gender filter
                    if (genderFilter && genderValue !== genderFilter) {
                        return false;
                    }

                    return true;
                }
            );

            // Event listeners for filter dropdowns
            $('#filter_pwd, #filter_senior, #filter_gender').on('change', function() {
                table.draw();
            });

            // IMPROVED: Enhanced image error handling
            $('img').on('error', function() {
                if (!$(this).hasClass('error-handled')) {
                    $(this).addClass('error-handled');
                    $(this).attr('src', 'image/person-icon.png');
                    $(this).addClass('default-image');
                }
            });
            
            // Preload default image to ensure it's available
            var defaultImg = new Image();
            defaultImg.src = 'image/person-icon.png';

            // Handle upload form submission
            $("#uploadForm").on("submit", function(e) {
                e.preventDefault();
                
                var fileInput = document.getElementById('fileInput');
                if (!fileInput.files[0]) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'No File Selected',
                        text: 'Please select a file to upload.'
                    });
                    return;
                }

                // Show loading overlay
                $("#uploadOverlay").show();
                $("#uploadBtn").prop('disabled', true);
                
                // Create FormData
                var formData = new FormData();
                formData.append('residents_file', fileInput.files[0]);
                formData.append('ajax_upload', '1');
                
                // AJAX upload
                $.ajax({
                    url: window.location.href,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    timeout: 300000, // 5 minutes
                    success: function(response) {
                        $("#uploadOverlay").hide();
                        $("#uploadBtn").prop('disabled', false);
                        
                        try {
                            if (typeof response === 'string') {
                                response = JSON.parse(response);
                            }
                            
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Upload Successful!',
                                    html: response.message,
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    $('#uploadResidentModal').modal('hide');
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Upload Failed',
                                    html: response.message,
                                    width: '600px'
                                });
                            }
                        } catch (e) {
                            console.error('Response parsing error:', e);
                            Swal.fire({
                                icon: 'error',
                                title: 'Upload Error',
                                text: 'There was an error processing the response.'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        $("#uploadOverlay").hide();
                        $("#uploadBtn").prop('disabled', false);
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Upload Failed',
                            text: 'Network error: ' + error
                        });
                        console.error('Upload error:', error);
                    }
                });
            });

            // Delete selected residents
            $("#deleteSelectedBtn").on("click", function(e) {
                e.preventDefault();
                
                var selected = $('input[name="chk_delete[]"]:checked');
                if (selected.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'No Selection',
                        text: 'Please select at least one resident to delete.'
                    });
                    return;
                }

                Swal.fire({
                    title: 'Confirm Delete',
                    text: `Delete ${selected.length} selected resident(s)?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#deleteForm').append('<input type="hidden" name="btn_delete" value="1">');
                        $('#deleteForm').submit();
                    }
                });
            });

            // Reset modal when closed
            $('#uploadResidentModal').on('hidden.bs.modal', function() {
                clearFile();
                $("#uploadOverlay").hide();
            });

            // FIXED: Enhanced image preview for file uploads in add modal
            $('input[type="file"][accept*="image"]').on('change', function() {
                var input = this;
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        // Find any existing preview in the same form group
                        var preview = $(input).closest('.form-group').find('.profile-image-preview');
                        if (preview.length === 0) {
                            // Create preview if it doesn't exist (for add modal)
                            var previewHtml = '<div class="mb-2"><img src="' + e.target.result + '" class="profile-image-preview mb-2" alt="Image Preview"></div>';
                            $(input).before(previewHtml);
                        } else {
                            // Update existing preview (for edit modal)
                            preview.attr('src', e.target.result);
                        }
                    };
                    reader.readAsDataURL(input.files[0]);
                }
            });
        });

        // File handling functions
        function handleFileSelect(input) {
            if (input.files && input.files[0]) {
                var file = input.files[0];
                var fileName = file.name;
                var fileSize = (file.size / 1024 / 1024).toFixed(2);
                
                // Validate file type
                var allowedTypes = ['csv', 'xlsx', 'xls'];
                var fileExt = fileName.split('.').pop().toLowerCase();
                
                if (!allowedTypes.includes(fileExt)) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid File Type',
                        text: 'Please select a CSV, XLSX, or XLS file.'
                    });
                    clearFile();
                    return;
                }
                
                if (file.size > 5242880) { // 5MB
                    Swal.fire({
                        icon: 'error',
                        title: 'File Too Large',
                        text: 'File size must be less than 5MB.'
                    });
                    clearFile();
                    return;
                }
                
                $("#fileName").text(fileName + " (" + fileSize + " MB)");
                $("#fileInfo").show();
                $("#uploadBtn").prop('disabled', false);
                $("#uploadArea").hide();
            }
        }

        function clearFile() {
            $("#fileInput").val('');
            $("#fileInfo").hide();
            $("#uploadArea").show();
            $("#uploadBtn").prop('disabled', true);
        }

        function checkMain(obj) {
            $('.chk_delete').prop('checked', obj.checked);
        }

        // Drag and drop functionality
        $("#uploadArea").on('dragover dragenter', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).addClass('drag-over');
        });

        $("#uploadArea").on('dragleave', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).removeClass('drag-over');
        });

        $("#uploadArea").on('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).removeClass('drag-over');
            
            var files = e.originalEvent.dataTransfer.files;
            if (files.length > 0) {
                $("#fileInput")[0].files = files;
                handleFileSelect($("#fileInput")[0]);
            }
        });

        // Show session messages with improved error handling
        <?php if (isset($_SESSION['added']) && $_SESSION['added'] == 1): ?>
        Swal.fire({icon: 'success', title: 'Success!', text: 'Resident added successfully.'});
        <?php unset($_SESSION['added']); endif; ?>

        <?php if (isset($_SESSION['edit']) && $_SESSION['edit'] == 1): ?>
        Swal.fire({icon: 'success', title: 'Success!', text: 'Resident updated successfully.'});
        <?php unset($_SESSION['edit']); endif; ?>

        <?php if (isset($_SESSION['delete']) && $_SESSION['delete'] == 1): ?>
        Swal.fire({icon: 'success', title: 'Success!', text: 'Selected residents deleted successfully.'});
        <?php unset($_SESSION['delete']); endif; ?>

        <?php if (isset($_SESSION['duplicate_error']) && $_SESSION['duplicate_error'] == 1): ?>
        Swal.fire({icon: 'error', title: 'Duplicate Found', text: 'A resident with the same name and birthdate already exists.'});
        <?php unset($_SESSION['duplicate_error']); endif; ?>

        <?php if (isset($_SESSION['fk_constraint']) && $_SESSION['fk_constraint'] == 1): ?>
        Swal.fire({icon: 'error', title: 'Cannot Delete', text: 'Cannot delete resident because they have related records.'});
        <?php unset($_SESSION['fk_constraint']); endif; ?>

        <?php if (isset($_SESSION['add_error'])): ?>
        Swal.fire({icon: 'error', title: 'Add Error', text: '<?php echo addslashes($_SESSION['add_error']); ?>'});
        <?php unset($_SESSION['add_error']); endif; ?>

        <?php if (isset($_SESSION['edit_error'])): ?>
        Swal.fire({icon: 'error', title: 'Edit Error', text: '<?php echo addslashes($_SESSION['edit_error']); ?>'});
        <?php unset($_SESSION['edit_error']); endif; ?>

        // IMPROVED: Debug function for troubleshooting image issues
        function debugImagePath(imageName) {
            console.log('Image name:', imageName);
            console.log('Expected path:', 'image/' + imageName);
            
            // Test if image exists by creating a temporary image object
            var img = new Image();
            img.onload = function() {
                console.log('Image loaded successfully:', imageName);
            };
            img.onerror = function() {
                console.log('Image failed to load:', imageName);
            };
            img.src = 'image/' + imageName;
        }
    </script>

    <!-- IMPROVED: Additional debugging information (remove in production) -->
    <script>
        // Log current directory and image directory status
        console.log('Current page:', window.location.pathname);
        console.log('Image directory should be at: image/');
        
        // Test default image availability
        var testImg = new Image();
        testImg.onload = function() {
            console.log('Default image (person-icon.png) is available');
        };
        testImg.onerror = function() {
            console.warn('Default image (person-icon.png) is missing! Please ensure it exists in the image/ directory.');
        };
        testImg.src = 'image/person-icon.png';
    </script>
</body>
</html>