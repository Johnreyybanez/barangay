<?php
session_start();
if (!isset($_SESSION['role'])) {
    header("Location: ../../login.php");
    exit();
}

include "../connection.php";
include_once "function.php";
include('../head_css.php');

// ✅ DELETE FUNCTION
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete_selected"])) {
    if (!empty($_POST["chk_delete"])) {
        $ids = implode(",", $_POST["chk_delete"]); // Convert array to comma-separated values
        $deleteQuery = "DELETE FROM assistance_requests WHERE request_id IN ($ids)";
        
        if (mysqli_query($con, $deleteQuery)) {
            $_SESSION["success"] = "Selected records deleted successfully.";
        } else {
            $_SESSION["error"] = "Error deleting records: " . mysqli_error($con);
        }
    } else {
        $_SESSION["delete_error"] = "No records selected."; // ✅ Moved to a separate session variable
    }
    header("Location: assistance_requests.php");
    exit();
}

?>

<body class="skin-black">
    <?php include('../header.php'); ?>

    <div class="wrapper row-offcanvas row-offcanvas-left">
        <?php include('../sidebar-left.php'); ?>

        <aside class="right-side">
            <section class="content-header">
                <h1>Assistance Requests</h1>
            </section>

            <section class="content">
                <div class="row">
                    <div class="box">
                        <div class="box-header">
                            <div style="padding:10px;">
                                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addModal">
                                    <i class="fa fa-plus" aria-hidden="true"></i> Add Request
                                </button>  
                                <button class="btn btn-danger btn-sm" id="deleteSelectedBtn">
                                    <i class="fas fa-trash-alt" aria-hidden="true"></i> Delete 
                                </button> 
                            </div> 
                              <hr>                                
                        </div>
                        <div class="box-body table-responsive">
                            <!-- Success/Error Messages via SweetAlert -->
                            <?php
                            if (isset($_SESSION["success"])) {
                                echo '<script type="text/javascript">
                                        Swal.fire({
                                            icon: "success",
                                            title: "Success!",
                                            text: "' . $_SESSION["success"] . '",
                                            showConfirmButton: true,
                                            
                                        });
                                      </script>';
                                unset($_SESSION["success"]);
                            }

                            if (isset($_SESSION["error"])) {
                                echo '<script type="text/javascript">
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
                            <form method="post" id="bulkDeleteForm">
                                <input type="hidden" name="delete_selected" value="1">
                                <table id="table" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th style="width: 20px !important;">
                                                <input type="checkbox" id="selectAll" />
                                            </th>
                                            <th>Resident Name</th>
                                            <th>Service Type</th>
                                            <th>Request Date</th>
                                            <th>Status</th>
                                            <th>Document</th>
                                            <th style="width: 40px !important;">Option</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                        $query = "SELECT ar.request_id, 
                                                        CONCAT(r.fname, ' ', r.mname, ' ', r.lname) AS resident_name, 
                                                        st.type_name, ar.request_date, ar.status, ar.document_path
                                                FROM assistance_requests ar
                                                JOIN tblresident r ON ar.resident_id = r.id
                                                JOIN service_types st ON ar.service_type_id = st.service_type_id";

                                        $squery = mysqli_query($con, $query);

                                        if (!$squery) {
                                            die("<p style='color:red;'>Query failed: " . mysqli_error($con) . "</p>");
                                        }

                                        while ($row = mysqli_fetch_array($squery)) {
                                            // Start the table row
                                            echo '<tr>
                                                    <td><input type="checkbox" class="chk_delete" name="chk_delete[]" value="' . htmlspecialchars($row['request_id']) . '" /></td>
                                                    <td>' . htmlspecialchars($row['resident_name']) . '</td>
                                                    <td>' . htmlspecialchars($row['type_name']) . '</td>
                                                    <td>' . htmlspecialchars($row['request_date']) . '</td>
                                                    <td>' . htmlspecialchars($row['status']) . '</td>
                                                    <td>';
                                            
                                            // Check if document exists and display accordingly
                                            if (!empty($row['document_path'])) {
                                                echo '<a href="' . htmlspecialchars($row['document_path']) . '" target="_blank">View</a>';
                                            } else {
                                                echo 'No Document';
                                            }

                                            echo '</td>
                                                <td>
                                                    <button class="btn btn-primary btn-sm edit-btn" data-toggle="modal" data-target="#editModal' . htmlspecialchars($row['request_id']) . '">
                                                        <i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit
                                                    </button>
                                                </td>
                                                </tr>';
                                            
                                            // Include the modal for each row
                                            include "edit_modal.php"; 
                                        }
                                        ?>

                                    </tbody>
                                </table>
                            </form>
                        </div>
                    </div>

                    <?php include "../edit_notif.php"; ?>
                    <?php include "../added_notif.php"; ?>
                    <?php include "../delete_notif.php"; ?>
                    <?php include "../duplicate_error.php"; ?>

                    <?php include "add_modal.php"; ?>
                </div>
            </section>
        </aside>
    </div>

    <?php include "../footer.php"; ?>
    <script type="text/javascript">
    $(document).ready(function() {
        $("#table").dataTable({
            "aoColumnDefs": [{ "bSortable": false, "aTargets": [0, 6] }],
            "aaSorting": []
        });

        // ✅ Select all checkboxes
        $("#selectAll").on("change", function() {
            $(".chk_delete").prop("checked", this.checked);
        });

        // ✅ Bulk Delete Action
        $("#deleteSelectedBtn").on("click", function(event) {
            if ($(".chk_delete:checked").length === 0) {
                // SweetAlert: No records selected
                Swal.fire({
                    icon: "warning",
                    title: "No Records Selected",
                    text: "Please select records to delete.",
                    showConfirmButton: true
                });
                return false;
            } else {
                // SweetAlert: Confirmation before deletion
                Swal.fire({
                    icon: "warning",
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    showCancelButton: true,
                    confirmButtonText: "Yes, delete it!",
                    cancelButtonText: "Cancel",
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#aaa",
                }).then((result) => {
                    if (result.isConfirmed) {
                        // If confirmed, submit the delete form
                        $("#bulkDeleteForm").submit();
                    }
                });
            }
        });
    });
    </script>

</html>
