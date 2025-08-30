<?php
include "../connection.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Financial Report Receipt</title>
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Courier New', monospace;
            width: 80mm;
            margin: 0 auto;
            padding: 20px;
            background: #fff;
        }

        h4, h5, h6 {
            text-align: center;
            margin: 0;
        }

        .receipt-line {
            border-top: 1px dashed #000;
            margin: 10px 0;
        }

        table {
            width: 100%;
            font-size: 12px;
        }

        th, td {
            text-align: left;
            padding: 2px 0;
        }

        .no-print {
            text-align: center;
            margin-bottom: 10px;
        }

        @media print {
            .no-print {
                display: none;
            }

            body {
                margin: 0;
                padding: 0;
            }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <button class="btn btn-primary btn-sm" onclick="window.print();">
            <i class="fa fa-print"></i> Print Receipt
        </button>
    </div>

    <h4>Barangay Financial Report</h4>
    <h6>Date Printed: <?php echo date("F d, Y"); ?></h6>
    <div class="receipt-line"></div>

    <?php
    $query = "SELECT * FROM financial_reports ORDER BY transaction_date ASC";
    $result = mysqli_query($con, $query);

    while ($row = mysqli_fetch_assoc($result)) {
        echo "
        <table>
            <tr><th>Type:</th><td>{$row['report_type']}</td></tr>
            <tr><th>Balance:</th><td>₱" . number_format($row['balance'], 2) . "</td></tr>
            <tr><th>Amount:</th><td>₱" . number_format($row['amount'], 2) . "</td></tr>
            <tr><th>Description:</th><td>{$row['description']}</td></tr>
            <tr><th>Date:</th><td>{$row['transaction_date']}</td></tr>
        </table>
        <div class='receipt-line'></div>";
    }
    ?>

    <h5>*** END OF REPORT ***</h5>

</body>
</html>
