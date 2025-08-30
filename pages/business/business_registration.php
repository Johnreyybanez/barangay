<?php
session_start();
include "../connection.php";

// Handle Bulk Delete Request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete_selected"])) {
    if (!empty($_POST["chk_delete"])) {
        $ids = implode(",", array_map('intval', $_POST["chk_delete"])); // Securely convert IDs to integers
        $query = "DELETE FROM business_registrations WHERE business_id IN ($ids)";
        if (mysqli_query($con, $query)) {
            $_SESSION["message"] = count($_POST["chk_delete"]) . " business registrations deleted successfully.";
        } else {
            $_SESSION["error"] = "Error deleting records.";
        }
    } else {
        $_SESSION["error"] = "No records selected.";
    }
    header("Location: business_registration.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Business Registration</title>
    <?php include '../head_css.php'; ?>
</head>
<body class="skin-black">
    <?php include '../header.php'; ?>

    <div class="wrapper row-offcanvas row-offcanvas-left">
        <?php include '../sidebar-left.php'; ?>

        <aside class="right-side">
            <section class="content-header">
                <h1>Business Registrations</h1>
            </section>

            <section class="content">
                <div class="row">
                    <div class="box">
                        <div class="box-header">
                            <div style="padding:10px;">
                                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addModal">
                                    <i class="fa fa-plus"></i> Add Business
                                </button>  
                                <button class="btn btn-danger btn-sm" id="deleteSelectedBtn">
                                    <i class="fas fa-trash-alt"></i> Delete 
                                </button> 
                            </div>
                              <hr>
                        </div>

                        <div class="box-body table-responsive">
                            <form method="post" id="bulkDeleteForm">
                                <table id="table" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th style="width: 20px !important;">
                                                <input type="checkbox" id="selectAll" />
                                            </th>
                                            <th>Business Name</th>
                                            <th>Owner</th>
                                            <th>Business Type</th>
                                            <th>Registration Date</th>
                                            <th>Validity Period</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                        $query = "SELECT br.business_id, br.business_name, 
                                                        CONCAT(r.fname, ' ', r.lname) AS owner_name, 
                                                        br.business_type, br.registration_date, br.validity_period
                                                FROM business_registrations br
                                                JOIN tblresident r ON br.owner_id = r.id";
                                        $result = mysqli_query($con, $query);

                                        while ($row = mysqli_fetch_assoc($result)) {
                                            echo "<tr>
                                                    <td><input type='checkbox' class='chk_delete' name='chk_delete[]' value='{$row['business_id']}' /></td>
                                                    <td>{$row['business_name']}</td>
                                                    <td>{$row['owner_name']}</td>
                                                    <td>{$row['business_type']}</td>
                                                    <td>{$row['registration_date']}</td>
                                                    <td>{$row['validity_period']}</td>
                                                    <td>
                                                        <button class='btn btn-primary btn-sm' data-toggle='modal' data-target='#editModal{$row['business_id']}'>
                                                            <i class='fa fa-edit'></i> Edit
                                                        </button>
                                                        <form method='post' style='display:inline;'>
                                                            <input type='hidden' name='business_id' value='{$row['business_id']}'>
                                                        </form>
                                                        <!-- Generate Button -->
                                                        <a href='form.php?business_id={$row['business_id']}' class='btn btn-success btn-sm'>
                                                            <i class='fa fa-file'></i> Generate
                                                        </a>
                                                    </td>
                                                </tr>";
                                            include "edit_modal.php"; // Include edit modal for each row
                                        }
                                        ?>

                                    </tbody>
                                </table>
                                <input type="hidden" name="delete_selected" value="1">
                            </form>
                        </div>
                    </div>
                </div>

            </section>
        </aside>
    </div>

    <?php include "add_modal.php"; ?>
    <?php include "../footer.php"; ?>

    <script type="text/javascript">
    $(function () {
        $("#table").dataTable();

        // Select All Checkboxes
        $("#selectAll").on("change", function () {
            $(".chk_delete").prop("checked", this.checked);
        });

        // SweetAlert Confirmation for Bulk Delete
        $("#deleteSelectedBtn").on("click", function () {
            if ($(".chk_delete:checked").length > 0) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'Selected records will be permanently deleted.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $("#bulkDeleteForm").submit();
                    }
                });
            } else {
                Swal.fire({
                    icon: 'info',
                    title: 'No Selection',
                    text: 'Please select at least one business registration to delete.',
                    confirmButtonColor: '#3085d6'
                });
            }
        });

        // SweetAlert Feedback Messages
        <?php if (isset($_SESSION['message'])): ?>
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: '<?php echo addslashes($_SESSION["message"]); ?>',
            confirmButtonColor: '#28a745'
        });
        <?php unset($_SESSION['message']); endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '<?php echo addslashes($_SESSION["error"]); ?>',
            confirmButtonColor: '#d33'
        });
        <?php unset($_SESSION['error']); endif; ?>
    });
</script>
</body>
</html>
