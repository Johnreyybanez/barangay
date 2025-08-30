<?php
session_start();
include "../connection.php";

// Handle Bulk Delete Request for Senior PWD Services
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete_selected"])) {
    if (!empty($_POST["chk_delete"])) {
        $ids = implode(",", array_map('intval', $_POST["chk_delete"])); // Securely convert IDs to integers
        $query = "DELETE FROM senior_pwd_services WHERE service_id IN ($ids)";
        if (mysqli_query($con, $query)) {
            $_SESSION["message"] = count($_POST["chk_delete"]) . " senior PWD services deleted successfully.";
        } else {
            $_SESSION["error"] = "Error deleting records.";
        }
    } else {
        $_SESSION["error"] = "No records selected.";
    }
    header("Location: senior_pwd.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Senior PWD Services</title>
    <?php include '../head_css.php'; ?>
</head>
<body class="skin-black">
    <?php include '../header.php'; ?>

    <div class="wrapper row-offcanvas row-offcanvas-left">
        <?php include '../sidebar-left.php'; ?>

        <aside class="right-side">
            <section class="content-header">
                <h1>Senior PWD Services</h1>
            </section>

            <section class="content">
                <div class="row">
                    <div class="box">
                        <div class="box-header">
                            <div style="padding:10px;">
                                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addModal">
                                    <i class="fa fa-plus"></i> Add Service
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
                                            <th>Resident Name</th>
                                            <th>Service Type</th>
                                            <th>Service Date</th>
                                            <th style="width: 40px !important;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                        $query = "SELECT s.service_id, r.fname, r.lname, st.type_name, s.service_date
                                                  FROM senior_pwd_services s
                                                  JOIN tblresident r ON s.resident_id = r.id
                                                  JOIN service_types st ON s.service_type_id = st.service_type_id";
                                        $result = mysqli_query($con, $query);

                                        while ($row = mysqli_fetch_assoc($result)) {
                                            echo "<tr>
                                                    <td><input type='checkbox' class='chk_delete' name='chk_delete[]' value='{$row['service_id']}' /></td>
                                                    <td>{$row['fname']} {$row['lname']}</td>
                                                    <td>{$row['type_name']}</td>
                                                    <td>{$row['service_date']}</td>
                                                    <td>
                                                        <button class='btn btn-primary btn-sm' data-toggle='modal' data-target='#editModal{$row['service_id']}'>
                                                            <i class='fa fa-edit'></i> Edit
                                                        </button>
                                                        <form method='post' style='display:inline;' action=''>
                                                            <input type='hidden' name='service_id' value='{$row['service_id']}'>
                                                        </form>
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

        // Select/Deselect all checkboxes
        $("#selectAll").on("change", function () {
            $(".chk_delete").prop("checked", this.checked);
        });

        // SweetAlert2 Delete Confirmation
        $("#deleteSelectedBtn").on("click", function (e) {
            e.preventDefault();
            const selected = $(".chk_delete:checked");
            if (selected.length === 0) {
                Swal.fire({
                    icon: 'info',
                    title: 'No Selection',
                    text: 'Please select at least one record to delete.',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }

            Swal.fire({
                title: 'Are you sure?',
                text: "You are about to delete " + selected.length + " record(s).",
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
        });

        // SweetAlert2 feedback messages
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
            confirmButtonColor: '#dc3545'
        });
        <?php unset($_SESSION['error']); endif; ?>
    });
</script>
</body>
</html>

