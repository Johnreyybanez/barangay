<?php
session_start();
include "../connection.php";

// Helper: Insert Notification
function insertNotification($con, $user_id, $message, $icon = 'fa fa-info-circle') {
    if (!$user_id) return;
    $stmt = $con->prepare("INSERT INTO notifications (user_id, message, icon, status) VALUES (?, ?, ?, 'unread')");
    $stmt->bind_param("iss", $user_id, $message, $icon);
    $stmt->execute();
    $stmt->close();
}

// Get session user details
$current_user_id = $_SESSION['id'] ?? $_SESSION['userid'] ?? 0;
$current_role = $_SESSION['role'] ?? 'Unknown';

// ✅ Handle Bulk Delete Request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete_selected"])) {
    if (!empty($_POST["chk_delete"])) {
        $ids = array_map('intval', $_POST["chk_delete"]);
        $placeholders = implode(",", array_fill(0, count($ids), "?"));

        $stmt = $con->prepare("DELETE FROM barangay_clearances WHERE clearance_id IN ($placeholders)");
        $stmt->bind_param(str_repeat("i", count($ids)), ...$ids);

        if ($stmt->execute()) {
            $count = count($ids);
            $_SESSION["message"] = "$count barangay clearance(s) deleted successfully.";

            $action = "Deleted $count barangay clearance record(s): IDs [" . implode(", ", $ids) . "]";
            mysqli_query($con, "INSERT INTO tbllogs (user, logdate, action) VALUES ('$current_role', NOW(), '$action')");
            insertNotification($con, $current_user_id, $action, "fa fa-trash");
        } else {
            $_SESSION["error_delete"] = "Error deleting records: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $_SESSION["error_delete"] = "No records selected.";
    }

    header("Location: clearances.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Barangay Clearances</title>
    <?php include '../head_css.php'; ?>
</head>
<body class="skin-black">
    <?php include '../header.php'; ?>

    <div class="wrapper row-offcanvas row-offcanvas-left">
        <?php include '../sidebar-left.php'; ?>

        <aside class="right-side">
            <section class="content-header">
                <h1>Barangay Clearances</h1>
            </section>

            <section class="content">
                <div class="row">
                    <div class="box">
                        <div class="box-header">
                            <div style="padding:10px;">
                                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addModal">
                                    <i class="fa fa-plus"></i> Add Clearance
                                </button>  
                                <button class="btn btn-danger btn-sm" id="deleteSelectedBtn">
                                    <i class="fas fa-trash-alt"></i> Delete 
                                </button> 
                            </div>
                              <hr>
                        </div>

                        <div class="box-body table-responsive">
                            <!-- BULK DELETE FORM -->
                            <form method="post" id="bulkDeleteForm">
                                <table id="table" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th style="width: 20px !important;">
                                                <input type="checkbox" id="selectAll" />
                                            </th>
                                            <th>Resident Name</th>
                                            <th>Clearance Type</th>
                                            <th>Issued By</th>
                                            <th>Issue Date</th>
                                            <th>Document</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                        // Fetch Barangay Clearances
                                        // Update this query to include clearance_type_id
                                        $query = "SELECT bc.clearance_id, 
                                                        CONCAT(r.fname, ' ', r.lname) AS resident_name, 
                                                        ct.type_name AS clearance_type,
                                                        ct.clearance_type_id, 
                                                        CONCAT(o.sPosition, ' ', o.completeName) AS issued_by, 
                                                        bc.issue_date, 
                                                        bc.document_path 
                                                FROM barangay_clearances bc
                                                JOIN tblresident r ON bc.resident_id = r.id
                                                JOIN clearance_types ct ON bc.clearance_type_id = ct.clearance_type_id
                                                JOIN tblofficial o ON bc.issued_by = o.id";

                                        $result = mysqli_query($con, $query);

                                        if (!$result) {
                                            echo "<tr><td colspan='7' class='text-danger'>Query Error: " . mysqli_error($con) . "</td></tr>";
                                        } else {
                                            // Loop through the results
                                            while ($row = mysqli_fetch_assoc($result)) {
                                                // Ensure that 'clearance_id' exists
                                                if (isset($row['clearance_id']) && !empty($row['clearance_id'])) {
                                                    echo "<tr>
                                                            <td>
                                                                <input type='checkbox' class='chk_delete' name='chk_delete[]' value='{$row['clearance_id']}' />
                                                            </td>
                                                            <td>{$row['resident_name']}</td>
                                                            <td>{$row['clearance_type']}</td>
                                                            <td>{$row['issued_by']}</td>
                                                            <td>{$row['issue_date']}</td>
                                                            <td><a href='{$row['document_path']}' target='_blank'>View</a></td>
                                                            <td>
                                                                <!-- Edit Button (Opens Modal) -->
                                                                <button type='button' class='btn btn-primary btn-sm edit-btn' 
                                                                    data-id='{$row['clearance_id']}' data-toggle='modal' data-target='#editModal'>
                                                                    <i class='fa fa-edit'></i> Edit
                                                                </button>
                                                                
                                                            <!-- Generate Document Button -->
                                                                <a href='clearance_form.php?clearance_id={$row['clearance_id']}&clearance_type_id={$row['clearance_type_id']}' 
                                                                class='btn btn-info btn-sm'>
                                                                    <i class='fa fa-file-text'></i> Generate Document
                                                                </a>

                                                            </td>
                                                        </tr>";
                                                } else {
                                                    echo "<tr><td colspan='7' class='text-danger'>Missing Clearance ID for Resident: {$row['resident_name']}</td></tr>";
                                                }
                                            }
                                        }
                                        ?>
                                   </tbody>
                                </table>
                                <input type="hidden" name="delete_selected" value="1">
                            </form>
                        </div>
                    </div>
                </div>
                <?php
                // Convert session messages to JS SweetAlert
                if (isset($_SESSION['message'])) {
                    echo "<script>
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: '" . $_SESSION['message'] . "'
                        });
                    </script>";
                    unset($_SESSION['message']);
                }

                if (isset($_SESSION['error_delete'])) {
                    echo "<script>
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: '" . $_SESSION['error_delete'] . "'
                        });
                    </script>";
                    unset($_SESSION['error_delete']);
                }
                ?>

            </section>
        </aside>
    </div>

    <?php include "add_modal.php"; ?>
    <?php include "../footer.php"; ?>
    <?php
        $query = "SELECT bc.clearance_id, bc.resident_id, bc.clearance_type_id, bc.issued_by, bc.issue_date, bc.document_path, 
                        r.fname, r.lname, ct.type_name, o.sPosition, o.completeName
                FROM barangay_clearances bc
                JOIN tblresident r ON bc.resident_id = r.id
                JOIN clearance_types ct ON bc.clearance_type_id = ct.clearance_type_id
                JOIN tblofficial o ON bc.issued_by = o.id";
        $result = mysqli_query($con, $query);

        while ($row = mysqli_fetch_assoc($result)): 
        ?>
    <!-- Edit Barangay Clearance Modal (REUSABLE) -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="function.php" enctype="multipart/form-data">
                <div class="modal-header">
                    <h4 class="modal-title">Edit Barangay Clearance</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <!-- Clearance ID (Hidden Field) -->
                    <input type="hidden" id="edit_clearance_id" name="clearance_id">

                    <!-- Resident Name -->
                    <div class="form-group">
                        <label>Resident Name</label>
                        <input type="text" class="form-control" value="<?php echo $row['fname'] . ' ' . $row['lname']; ?>" readonly>
                    </div>

                    <!-- Clearance Type -->
                    <div class="form-group">
                        <label>Clearance Type</label>
                        <select id="edit_clearance_type" name="clearance_type_id" class="form-control" required>
                            <?php
                            $type_query = "SELECT * FROM clearance_types";
                            $type_result = mysqli_query($con, $type_query);
                            while ($type = mysqli_fetch_assoc($type_result)) {
                                echo "<option value='{$type['clearance_type_id']}'>{$type['type_name']}</option>";
                            }
                            ?>
                        </select>
                    </div>

                   

                    <!-- Issued By -->
                    <div class="form-group">
                        <label>Issued By</label>
                        <select name="issued_by" class="form-control" required>
                            <?php
                            $official_query = "SELECT * FROM tblofficial";
                            $official_result = mysqli_query($con, $official_query);
                            while ($official = mysqli_fetch_assoc($official_result)) {
                                $selected = ($official['id'] == $row['issued_by']) ? "selected" : "";
                                echo "<option value='{$official['id']}' $selected>{$official['sPosition']} - {$official['completeName']}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <!-- Issue Date -->
                    <div class="form-group">
                        <label>Issue Date</label>
                        <input type="date" name="issue_date" class="form-control" value="<?php echo $row['issue_date']; ?>" required>
                    </div>

                    <!-- Current Document -->
                    <div class="form-group">
                        <label>Current Document</label><br>
                        <?php if (!empty($row['document_path'])): ?>
                            <a href="<?php echo $row['document_path']; ?>" target="_blank">View Current Document</a>
                        <?php else: ?>
                            <p>No document uploaded.</p>
                        <?php endif; ?>
                    </div>

                    <!-- Upload New Document -->
                    <div class="form-group">
                        <label>Upload New Document (Optional)</label>
                        <input type="file" name="document" class="form-control">
                        <input type="hidden" name="existing_document" value="<?php echo $row['document_path']; ?>">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" name="update_clearance" class="btn btn-success">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endwhile; ?>


    <!-- JavaScript -->
    <script type="text/javascript">
        $(function() {
            $("#table").dataTable();

            $("#selectAll").on("change", function() {
                $(".chk_delete").prop("checked", this.checked);
            });

            $("#deleteSelectedBtn").on("click", function() {
            if ($(".chk_delete:checked").length > 0) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete them!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $("#bulkDeleteForm").submit();
                    }
                });
            } else {
                Swal.fire({
                    icon: 'info',
                    title: 'No Selection',
                    text: 'Please select at least one record to delete.'
                });
            }
        });
            $(".edit-btn").on("click", function() {
                var clearanceId = $(this).data("id");
                $("#edit_clearance_id").val(clearanceId);
                $("#editModal").modal("show");
            });
        });
    </script>

</body>
</html>
