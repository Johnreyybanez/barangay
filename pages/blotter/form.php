<?php
session_start();
include '../connection.php';

// Check if user is logged in
if (!isset($_SESSION['role'])) {
    header("Location: ../../login.php");
    exit();
}

// Check if blotter_id is provided in URL
if (!isset($_GET['blotter_id']) || empty($_GET['blotter_id'])) {
    echo "No blotter record specified.";
    exit();
}

$blotter_id = intval($_GET['blotter_id']); // Sanitize input

// Fetch blotter details with proper error handling
$query = mysqli_query($con, "
    SELECT 
        b.*, 
        c.lname AS complainant_lname, c.fname AS complainant_fname, 
        c.mname AS complainant_mname, c.address AS complainant_address,
        r.lname AS respondent_lname, r.fname AS respondent_fname, 
        r.mname AS respondent_mname, r.address AS respondent_address
    FROM blotter_records AS b
    LEFT JOIN tblresident AS c ON b.complainant_id = c.id
    LEFT JOIN tblresident AS r ON b.respondent_id = r.id
    WHERE b.blotter_id = $blotter_id
");

// Check if query was successful
if (!$query) {
    echo "Database query failed: " . mysqli_error($con);
    exit();
}

if (mysqli_num_rows($query) == 0) {
    echo "Blotter record not found.";
    exit();
}

$row = mysqli_fetch_assoc($query);

// Get current barangay info for header
$barangay_query = mysqli_query($con, "SELECT * FROM settings LIMIT 1");
if (!$barangay_query) {
    echo "Error fetching barangay information: " . mysqli_error($con);
    exit();
}
$barangay_info = mysqli_fetch_assoc($barangay_query);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Blotter Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            font-size: 14px;
            line-height: 1.5;
        }
        .container {
    width: 100%;
    max-width: 1000px;
    margin: 0 auto;
    padding: 20px 40px; /* Increased horizontal padding for better left/right margin */
    border: 1px solid #ddd;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
    box-sizing: border-box; /* ensures padding doesn't overflow */
}

        .header {
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            display: flex;
            justify-content: space-between; /* Ensures the logo is spaced correctly */
            align-items: center;
        }
        .header-text h3 {
    margin: 2px 0;
    font-size: 16px;
}

.header-text h2 {
    margin: 6px 0 0;
    font-size: 20px;
    font-weight: bold;
}

        .logo-container {
            flex: 0 0 120px;
            margin-left: 20px;
        }
        .header-text {
            text-align: center;
            flex-grow: 1; /* Ensures the header text takes up the remaining space */
        }
        .logo {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: contain;
            border: 1px solid #ddd;
        }
        .title {
            font-size: 20px;
            font-weight: bold;
            margin: 25px 0;
            text-align: center;
        }
        .section {
            margin-bottom: 30px;
        }
        .section-title {
            font-weight: bold;
            font-size: 16px;
            border-bottom: 1px solid #333;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
            padding: 10px;
            text-align: left;
            font-weight: bold;
            width: 30%;
        }
        td {
            padding: 10px;
        }
        .incident-details, .resolution-details {
            padding: 15px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
            text-align: justify;
            margin-bottom: 20px;
            min-height: 100px;
        }
        .signatures {
            margin-top: 70px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            width: 45%;
            text-align: center;
        }
        .signature-line {
            border-top: 1px solid #000;
            margin-top: 50px;
            padding-top: 5px;
            font-weight: bold;
        }
        .printBtn {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-bottom: 20px;
            font-weight: bold;
        }
        .printBtn:hover {
            background-color: #0056b3;
        }
        .official-seal {
            position: absolute;
            top: 20px;
            right: 20px;
            width: 100px;
            height: 100px;
            border: 1px dashed #999;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        @media print {
    .printBtn {
        display: none;
    }
    body {
        margin: 0;
        padding: 0;
    }
    .container {
        box-shadow: none;
        border: none;
        width: 100%;
        max-width: 100%;
        padding: 40px; /* Consistent padding for both sides when printing */
    }
}

    </style>
</head>
<body>
    <div class="container">
        <button class="printBtn" onclick="window.print()">Print Blotter Report</button>
        
        <div class="header">
            <div class="logo-container">
                <!-- Logo on the left side -->
                <img src="<?php echo '../../img/logo.png' . $barangay_info['image']; ?>" class="logo">


            </div>
            
            <div class="header-text">
                <h3>Republic of the Philippines</h3>
                <h3>Province of <?php echo isset($barangay_info['province']) ? $barangay_info['province'] : 'Province'; ?></h3>
                <h3>Municipality of <?php echo isset($barangay_info['city']) ? $barangay_info['city'] : 'Municipality'; ?></h3>
                <h2>Barangay <?php echo isset($barangay_info['barangay']) ? $barangay_info['barangay'] : 'Barangay Name'; ?></h2>
            </div>
            
            <div class="logo-container style">
                <!-- Logo on the right side -->
                <img src="<?php echo '../setting/uploads/' . $barangay_info['image']; ?>" class="logo">

            </div>
        </div>
        
        <div class="title">BLOTTER REPORT</div>
        
        <div class="section">
            <div class="section-title">BLOTTER INFORMATION</div>
            <table>
                <tr>
                    <th>Blotter ID</th>
                    <td><?php echo $row['blotter_id']; ?></td>
                </tr>
                <tr>
                    <th>Date Reported</th>
                    <td>
                        <?php 
                        if(isset($row['date_reported']) && !empty($row['date_reported'])) {
                            echo date('F d, Y', strtotime($row['date_reported']));
                        } else {
                            echo date('F d, Y');
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <th>Incident Date</th>
                    <td>
                        <?php 
                        if(isset($row['incident_date']) && !empty($row['incident_date'])) {
                            echo date('F d, Y', strtotime($row['incident_date']));
                        } else {
                            echo 'Not specified';
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td><?php echo $row['status']; ?></td>
                </tr>
            </table>
        </div>
        
        <div class="section">
            <div class="section-title">COMPLAINANT INFORMATION</div>
            <table>
                <tr>
                    <th>Name</th>
                    <td>
                        <?php 
                        $complainant_name = $row['complainant_lname'] . ', ' . $row['complainant_fname'];
                        if(isset($row['complainant_mname']) && !empty($row['complainant_mname'])) {
                            $complainant_name .= ' ' . $row['complainant_mname'];
                        }
                        echo $complainant_name;
                        ?>
                    </td>
                </tr>
                <tr>
                    <th>Address</th>
                    <td><?php echo isset($row['complainant_address']) ? $row['complainant_address'] : 'N/A'; ?></td>
                </tr>
            </table>
        </div>
        
        <div class="section">
            <div class="section-title">RESPONDENT INFORMATION</div>
            <table>
                <tr>
                    <th>Name</th>
                    <td>
                        <?php 
                        $respondent_name = $row['respondent_lname'] . ', ' . $row['respondent_fname'];
                        if(isset($row['respondent_mname']) && !empty($row['respondent_mname'])) {
                            $respondent_name .= ' ' . $row['respondent_mname'];
                        }
                        echo $respondent_name;
                        ?>
                    </td>
                </tr>
                <tr>
                    <th>Address</th>
                    <td><?php echo isset($row['respondent_address']) ? $row['respondent_address'] : 'N/A'; ?></td>
                </tr>
            </table>
        </div>
        
        <div class="section">
            <div class="section-title">INCIDENT DETAILS</div>
            <div class="incident-details">
                <?php echo nl2br(htmlspecialchars($row['incident_desc'])); ?>
            </div>
        </div>
        
        <?php if (isset($row['resolution']) && !empty($row['resolution'])): ?>
        <div class="section">
            <div class="section-title">RESOLUTION</div>
            <div class="resolution-details">
                <?php echo nl2br(htmlspecialchars($row['resolution'])); ?>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="signatures">
            <div class="signature-box">
                <div class="signature-line">Complainant's Signature</div>
            </div>
            <div class="signature-box">
                <div class="signature-line">Barangay Captain</div>
            </div>
        </div>
        
        <div style="text-align: center; margin-top: 50px; font-style: italic; font-size: 12px;">
            This document is officially issued by the Barangay <?php echo isset($barangay_info['barangay']) ? $barangay_info['barangay'] : ''; ?> on <?php echo date('F d, Y'); ?>
        </div>
    </div>
</body>
</html>
