<?php
// templates/business_permit_template.php
// Make sure this file is included from generate_document.php to have access to $clearance variable

// Set header for PDF generation (if using a PDF library)
header('Content-Type: text/html');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barangay Business Permit</title>
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
            border: 2px solid #000;
            background: linear-gradient(to bottom, rgba(255,255,255,0.9) 0%, rgba(255,255,255,0.9) 100%);
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
        .permit-title {
            font-size: 24px;
            font-weight: bold;
            margin: 20px 0;
            text-align: center;
            text-transform: uppercase;
            border: 3px solid #000;
            padding: 10px;
            background-color: rgba(255, 255, 255, 0.8);
        }
        .subtitle {
            font-size: 14px;
            margin: 5px 0;
        }
        .content {
            margin: 20px 0;
            text-align: justify;
        }
        .business-details {
            margin: 20px 0;
            border: 1px solid #000;
            padding: 15px;
            background-color: rgba(255, 255, 255, 0.7);
        }
        .business-detail-row {
            display: flex;
            margin-bottom: 10px;
        }
        .detail-label {
            font-weight: bold;
            width: 200px;
        }
        .validity {
            margin-top: 20px;
            text-align: center;
            font-weight: bold;
            font-size: 16px;
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
            padding: 10px 0;
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
        .permit-footer {
            margin-top: 20px;
            font-size: 12px;
            text-align: center;
            font-style: italic;
        }
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            opacity: 0.1;
            font-size: 8em;
            font-weight: bold;
            z-index: -1;
            color: #000;
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
        <div class="watermark">CERTIFIED</div>
        <div class="header">
            <div style="display: flex; justify-content: center; align-items: center;">
                <img src="../images/barangay_logo.jpg" alt="Barangay Logo" class="logo" style="margin-right: 20px;">
                <div>
                    <div class="title">Republic of the Philippines</div>
                    <div class="subtitle">Province of <?php echo htmlspecialchars($clearance['province'] ?? 'Province'); ?></div>
                    <div class="subtitle">Municipality of <?php echo htmlspecialchars($clearance['city'] ?? 'Municipality'); ?></div>
                    <div class="title">BARANGAY <?php echo htmlspecialchars($clearance['barangay'] ?? 'Barangay Name'); ?></div>
                </div>
                <img src="../images/city_logo.jpg" alt="City Logo" class="logo" style="margin-left: 20px;">
            </div>
            <div class="title" style="margin-top: 20px;">OFFICE OF THE BARANGAY CAPTAIN</div>
        </div>
        
        <div class="permit-title">BARANGAY BUSINESS PERMIT</div>
        
        <div class="content">
            <p>This is to certify that the business enterprise described below has complied with the requirements for the issuance of a Barangay Business Permit:</p>
            
            <div class="business-details">
                <div class="business-detail-row">
                    <div class="detail-label">Business Name:</div>
                    <div>_________________________________________</div>
                </div>
                <div class="business-detail-row">
                    <div class="detail-label">Owner/Proprietor:</div>
                    <div><?php echo htmlspecialchars($clearance['resident_name'] ?? ''); ?></div>
                </div>
                <div class="business-detail-row">
                    <div class="detail-label">Business Address:</div>
                    <div><?php echo htmlspecialchars($clearance['address'] ?? ''); ?>, Barangay <?php echo htmlspecialchars($clearance['barangay'] ?? ''); ?></div>
                </div>
                <div class="business-detail-row">
                    <div class="detail-label">Nature of Business:</div>
                    <div>_________________________________________</div>
                </div>
                <div class="business-detail-row">
                    <div class="detail-label">Permit Number:</div>
                    <div>BP-<?php echo date('Y'); ?>-<?php echo str_pad($clearance['clearance_id'] ?? '0001', 4, '0', STR_PAD_LEFT); ?></div>
                </div>
            </div>
            
            <div class="validity">
                VALID UNTIL DECEMBER 31, <?php echo date('Y'); ?>
            </div>
            
            <p style="margin-top: 30px;">Issued this <?php echo date('jS'); ?> day of <?php echo date('F Y'); ?> at Barangay <?php echo htmlspecialchars($clearance['barangay'] ?? ''); ?>, <?php echo htmlspecialchars($clearance['city'] ?? ''); ?>, <?php echo htmlspecialchars($clearance['province'] ?? ''); ?>.</p>
        </div>
        
        <div class="signature-section">
            <div class="signature">
                <div class="signature-line"><?php echo htmlspecialchars($clearance['resident_name'] ?? ''); ?></div>
                <div>Owner/Proprietor</div>
            </div>
            <div class="signature">
                <div class="signature-line">Barangay Captain</div>
                <div>Barangay <?php echo htmlspecialchars($clearance['barangay'] ?? ''); ?></div>
            </div>
        </div>
        
        <div class="permit-footer">
            <p>This permit must be displayed in a conspicuous place within the business premises.</p>
            <p>NOT VALID WITHOUT OFFICIAL SEAL</p>
        </div>
    </div>
    
    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()">Print Permit</button>
        <button onclick="window.location.href='clearances.php'">Back to List</button>
    </div>
</body>
</html>