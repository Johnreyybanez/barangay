<?php
session_start();
include "../connection.php";

// Helper: insert a notification
function insertNotification($con, $user_id, $message, $icon = 'fa fa-info-circle') {
    $stmt = $con->prepare("INSERT INTO notifications (user_id, message, icon, status) VALUES (?, ?, ?, 'unread')");
    $stmt->bind_param("iss", $user_id, $message, $icon);
    $stmt->execute();
    $stmt->close();
}

// Current logged-in user ID
$current_user_id = $_SESSION['id'] ?? $_SESSION['userid'] ?? 0;
$current_user_role = $_SESSION['role'] ?? 'Unknown';

// Calculate Totals
$totalamount = 0;
$incomeTotal = 0;
$expenseTotal = 0;

$amountQuery = mysqli_query($con, "SELECT LOWER(TRIM(report_type)) AS type, amount FROM financial_reports");
if ($amountQuery && mysqli_num_rows($amountQuery) > 0) {
    while ($row = mysqli_fetch_assoc($amountQuery)) {
        $type = $row['type'];
        $amount = floatval($row['amount']);
        if ($type === 'income' || $type === 'budget') $incomeTotal += $amount;
        if ($type === 'expense' || $type === 'expenses') $expenseTotal += $amount;
    }
    $totalamount = $incomeTotal - $expenseTotal;
}

// Handle Edit
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["edit_report"])) {
    $report_id = intval($_POST["report_id"]);
    $report_type = mysqli_real_escape_string($con, $_POST["report_type"]);
    $amount = mysqli_real_escape_string($con, $_POST["amount"]);
    $description = mysqli_real_escape_string($con, $_POST["description"]);
    $transaction_date = mysqli_real_escape_string($con, $_POST["transaction_date"]);
    $balance = mysqli_real_escape_string($con, $_POST["balance"]);

    $document_path = "";

    if (!empty($_FILES["document"]["name"])) {
        $target_dir = "../reports/uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $file_name = basename($_FILES["document"]["name"]);
        $target_file = $target_dir . time() . "_" . $file_name;
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $allowed_types = ["pdf", "doc", "docx", "jpg", "png"];
        if (in_array($file_type, $allowed_types)) {
            if (move_uploaded_file($_FILES["document"]["tmp_name"], $target_file)) {
                $document_path = $target_file;
            } else {
                $_SESSION["error"] = "Error uploading document.";
                header("Location: financial_reports.php");
                exit();
            }
        } else {
            $_SESSION["error"] = "Invalid file type.";
            header("Location: financial_reports.php");
            exit();
        }
    }

    if (empty($document_path)) {
        $query = "SELECT document_path FROM financial_reports WHERE report_id = $report_id";
        $result = mysqli_query($con, $query);
        $row = mysqli_fetch_assoc($result);
        $document_path = $row['document_path'];
    }

    $query = "UPDATE financial_reports SET 
                report_type='$report_type', 
                amount='$amount', 
                description='$description', 
                transaction_date='$transaction_date',
                document_path='$document_path',
                balance='$balance'
              WHERE report_id=$report_id";

    if (mysqli_query($con, $query)) {
        $action = "Updated financial report: ID $report_id ($report_type)";
        mysqli_query($con, "INSERT INTO tbllogs (user, logdate, action) VALUES ('$current_user_role', NOW(), '$action')");
        insertNotification($con, $current_user_id, $action, "fa fa-edit");

        $_SESSION["message"] = "Financial report updated successfully.";
    } else {
        $_SESSION["error"] = "Error updating report.";
    }

    header("Location: financial_reports.php");
    exit();
}

// Bulk Delete
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete_selected"])) {
    if (!empty($_POST["chk_delete"])) {
        $ids = array_map('intval', $_POST["chk_delete"]);
        $ids_list = implode(",", $ids);
        $query = "DELETE FROM financial_reports WHERE report_id IN ($ids_list)";

        if (mysqli_query($con, $query)) {
            $count = count($ids);
            $action = "Deleted $count financial report(s): IDs [$ids_list]";
            mysqli_query($con, "INSERT INTO tbllogs (user, logdate, action) VALUES ('$current_user_role', NOW(), '$action')");
            insertNotification($con, $current_user_id, $action, "fa fa-trash");

            $_SESSION["message"] = "$count financial report(s) deleted successfully.";
        } else {
            $_SESSION["error"] = "Error deleting records.";
        }
    } else {
        $_SESSION["error"] = "No records selected.";
    }
    header("Location: financial_reports.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <title>Financial Reports</title>
    <?php include '../head_css.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="skin-black">
<?php include '../header.php'; ?>
<div class="wrapper row-offcanvas row-offcanvas-left">
    <?php include '../sidebar-left.php'; ?>
    <aside class="right-side">
        <section class="content-header">
            <h1>Financial Reports</h1>
        </section>

        <section class="content">
            <div class="row">
                <div class="box">
                    <div class="box-header">
                        <div style="padding:10px;">
                            <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addModal">
                                <i class="fa fa-plus"></i> Add Report
                            </button>
                            <button class="btn btn-danger btn-sm" id="deleteSelectedBtn">
                                <i class="fas fa-trash-alt"></i> Delete
                            </button>
                            <button class="btn btn-info btn-sm" onclick="exportTableToCSV('financial_reports.csv')">
                                <i class="fa fa-download"></i> Export CSV
                            </button>
                            <span class="badge bg-info" style="font-size: 16px; padding: 10px 15px;">
                                Total Balance: ₱<?php echo number_format($totalamount, 2); ?>
                            </span>
                        </div>
                          <hr>
                    </div>

                    <div class="box-body table-responsive">
                        <form method="post" id="bulkDeleteForm">
                            <table id="table" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" id="selectAll" /></th>
                                        <th>Type</th>
                                        <th>Balance</th>
                                        <th>Amount</th>
                                        <th>Description</th>
                                        <th>Transaction Date</th>
                                        <th>Document</th>
                                        <th style="width: 40px !important;">Option</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                $query = "SELECT * FROM financial_reports";
                                $result = mysqli_query($con, $query);
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo "<tr>
                                            <td><input type='checkbox' class='chk_delete' name='chk_delete[]' value='{$row['report_id']}' /></td>
                                            <td>{$row['report_type']}</td>
                                            <td>₱" . number_format($totalamount, 2) . "</td>
                                            <td>{$row['amount']}</td>
                                            <td>{$row['description']}</td>
                                            <td>{$row['transaction_date']}</td>
                                            <td>";
                                    echo (!empty($row['document_path'])) 
                                        ? '<a href="' . htmlspecialchars($row['document_path']) . '" target="_blank">View</a>' 
                                        : 'No Document';
                                    echo "</td>
                                            <td>
                                                <button class='btn btn-primary btn-sm' data-toggle='modal' data-target='#editModal{$row['report_id']}'>
                                                    <i class='fa fa-edit'></i> Edit
                                                </button>
                                            </td>
                                        </tr>";
                                    include "edit_modal.php";
                                }
                                ?>
                                </tbody>
                            </table>
                            <input type="hidden" name="delete_selected" value="1">
                        </form>
                    </div>
                </div>
            </div>

            <!-- SweetAlert session messages -->
            <?php
            if (isset($_SESSION["message"])) {
                echo '<script>
                    Swal.fire({
                        icon: "success",
                        title: "Success!",
                        text: "' . $_SESSION["message"] . '",
                       
                        showConfirmButton: true
                    });
                </script>';
                unset($_SESSION["message"]);
            }

            if (isset($_SESSION["error"])) {
                echo '<script>
                    Swal.fire({
                        icon: "error",
                        title: "Oops...",
                        text: "' . $_SESSION["error"] . '",
                       
                        showConfirmButton: true
                    });
                </script>';
                unset($_SESSION["error"]);
            }
            ?>
        </section>
    </aside>
</div>

<?php include "add_modal.php"; ?>
<?php include "../footer.php"; ?>

<script type="text/javascript">
$(function () {
    $("#table").dataTable();

    $("#selectAll").on("change", function () {
        $(".chk_delete").prop("checked", this.checked);
    });

    $("#deleteSelectedBtn").on("click", function () {
        const selected = $(".chk_delete:checked").length;
        if (selected > 0) {
            Swal.fire({
                title: `Delete ${selected} record(s)?`,
                text: "This action cannot be undone.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Yes, delete them!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $("#bulkDeleteForm").submit();
                }
            });
        } else {
            Swal.fire({
                icon: "info",
                title: "No records selected",
                text: "Please select at least one record to delete.",
               
                showConfirmButton: true
            });
        }
    });
});

function exportTableToCSV(filename) {
    var csv = [];
    var rows = document.querySelectorAll("table tr");

    for (var i = 0; i < rows.length; i++) {
        var row = [], cols = rows[i].querySelectorAll("td, th");
        for (var j = 0; j < cols.length; j++) {
            var cellText = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, "").trim();
            row.push('"' + cellText + '"');
        }
        csv.push(row.join(","));
    }

    var csvFile = new Blob([csv.join("\n")], { type: "text/csv" });
    var downloadLink = document.createElement("a");
    downloadLink.download = filename;
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.style.display = "none";
    document.body.appendChild(downloadLink);
    downloadLink.click();
}
</script>
</body>
</html>
