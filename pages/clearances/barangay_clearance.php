<?php
// templates/barangay_clearance_template.php
// Make sure this file is included from generate_document.php to have access to $clearance variable

// Set header for PDF generation (if using a PDF library)
header('Content-Type: text/html');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barangay Clearance</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #000;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo {
            width: 80px;
            height: 80px;
        }
        .title {
            font-size: 18px;
            font-weight: bold;
            margin: 10px 0;
        }
        .subtitle {
            font-size: 14px;
            margin: 5px 0;
        }
        .content {
            margin: 20px 0;
            text-align: justify;
        }
        .signature-section {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }
        .signature {
            width: 45%;
            text-align: center;
        }
        .signature-line {
            border-top: 1px solid #000;
            margin-top: 40px;
            padding-top: 5px;
        }
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div style="display: flex; justify-content: center; align-items: center;">
                <img src="../../img/logo.png" alt="Barangay Logo" class="logo" style="margin-right: 20px;">
                <div>
                    <div class="title">Republic of the Philippines</div>
                    <div class="subtitle">Province of <?php echo htmlspecialchars($clearance['province'] ?? 'Province'); ?></div>
                    <div class="subtitle">Municipality of <?php echo htmlspecialchars($clearance['city'] ?? 'Municipality'); ?></div>
                    <div class="title">BARANGAY <?php echo htmlspecialchars($clearance['barangay'] ?? 'Barangay Name'); ?></div>
                </div>
                <img src="../../img/logo.png" alt="City Logo" class="logo" style="margin-left: 20px;">
            </div>
            <div class="title" style="margin-top: 20px;">OFFICE OF THE BARANGAY CAPTAIN</div>
            <h2>BARANGAY CLEARANCE</h2>
        </div>
        
        <div class="content">
            <p>TO WHOM IT MAY CONCERN:</p>
            
            <p>This is to certify that <strong><?php echo htmlspecialchars($clearance['resident_name'] ?? ''); ?></strong>, 
            <?php echo htmlspecialchars($clearance['age'] ?? ''); ?> years old, 
            <?php echo htmlspecialchars($clearance['gender'] ?? ''); ?>, 
            <?php echo htmlspecialchars($clearance['civilStatus'] ?? ''); ?>, 
            is a bonafide resident of <?php echo htmlspecialchars($clearance['address'] ?? ''); ?>, 
            Barangay <?php echo htmlspecialchars($clearance['barangay'] ?? ''); ?>, 
            <?php echo htmlspecialchars($clearance['city'] ?? ''); ?>, 
            <?php echo htmlspecialchars($clearance['province'] ?? ''); ?>.</p>
            
            <p>This certification is being issued upon the request of the above-named person for <strong>GENERAL PURPOSES</strong>.</p>
            
            <p>Issued this <?php echo date('jS'); ?> day of <?php echo date('F Y'); ?> at Barangay <?php echo htmlspecialchars($clearance['barangay'] ?? ''); ?>, <?php echo htmlspecialchars($clearance['city'] ?? ''); ?>, <?php echo htmlspecialchars($clearance['province'] ?? ''); ?>.</p>
        </div>
        
        <div class="signature-section">
            <div class="signature">
                <div class="signature-line">Applicant's Signature</div>
            </div>
            <div class="signature">
                <div class="signature-line">Barangay Captain</div>
            </div>
        </div>
        
        <div style="margin-top: 40px;">
            <p><strong>Official Receipt No.:</strong> ________________</p>
            <p><strong>Issued on:</strong> <?php echo date('F j, Y'); ?></p>
            <p><strong>Issued at:</strong> Barangay <?php echo htmlspecialchars($clearance['barangay'] ?? ''); ?></p>
        </div>
    </div>
    
    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()">Print Clearance</button>
        <button onclick="window.location.href='clearances.php'">Back to List</button>
    </div>
</body>
</html>