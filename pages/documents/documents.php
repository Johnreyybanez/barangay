<?php
session_start();
include "../connection.php";

// Handle Bulk Delete
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete_selected"])) {
    if (!empty($_POST["chk_delete"])) {
        $ids = implode(",", array_map('intval', $_POST["chk_delete"]));
        $query = "DELETE FROM documents WHERE document_id IN ($ids)";
        if (mysqli_query($con, $query)) {
            $_SESSION["message"] = count($_POST["chk_delete"]) . " documents deleted successfully.";
        } else {
            $_SESSION["error"] = "Error deleting records.";
        }
    } else {
        $_SESSION["error"] = "No records selected.";
    }
    header("Location: documents.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Documents</title>
    <?php include '../head_css.php'; ?>
</head>
<body class="skin-black">
<?php include '../header.php'; ?>

<div class="wrapper row-offcanvas row-offcanvas-left">
    <?php include '../sidebar-left.php'; ?>

    <aside class="right-side">
        <section class="content-header">
            <h1>Documents</h1>
        </section>

        <section class="content">
            <div class="row">
                <div class="box">
                    <div class="box-header">
                        <div style="padding:10px;">
                            <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addModal">
                                <i class="fa fa-plus"></i> Add Document
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
                                    <th style="width: 20px;">
                                        <input type="checkbox" id="selectAll"/>
                                    </th>
                                    <th>Document Type</th>
                                    <th>Resident</th>
                                    <th>File Path</th>
                                    <th style="width: 40px;">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $query = "SELECT d.document_id, dt.type_name, CONCAT(r.fname, ' ', r.lname) AS resident_name, d.file_path
                                          FROM documents d
                                          JOIN document_types dt ON d.document_type_id = dt.document_type_id
                                          JOIN tblresident r ON d.resident_id = r.id";
                                $result = mysqli_query($con, $query);
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo "<tr>
                                            <td><input type='checkbox' class='chk_delete' name='chk_delete[]' value='{$row['document_id']}' /></td>
                                            <td>{$row['type_name']}</td>
                                            <td>{$row['resident_name']}</td>
                                            <td>{$row['file_path']}</td>
                                            <td>
                                                <button class='btn btn-primary btn-sm' data-toggle='modal' data-target='#editModal'
                                                        data-document_id='{$row['document_id']}'
                                                        data-document_type_id='{$row['type_name']}'
                                                        data-resident_id='{$row['resident_name']}'
                                                        data-file_path='{$row['file_path']}'>
                                                    <i class='fa fa-edit'></i> Edit
                                                </button>
                                            </td>
                                        </tr>";
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

<!-- Include Modals -->
<?php include "add_modal.php"; ?>
<?php include "../footer.php"; ?>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script type="text/javascript">
    $(document).ready(function () {
        $("#table").dataTable();

        // Select all checkboxes
        $("#selectAll").on("change", function () {
            $(".chk_delete").prop("checked", this.checked);
        });

        // SweetAlert delete confirmation
        $("#deleteSelectedBtn").on("click", function () {
            if ($(".chk_delete:checked").length > 0) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Selected document(s) will be deleted permanently.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $("#bulkDeleteForm").submit();
                    }
                });
            } else {
                Swal.fire({
                    icon: 'info',
                    title: 'No Selection',
                    text: 'Please select at least one document to delete.',
                    confirmButtonColor: '#3085d6'
                });
            }
        });

        // Populate Edit Modal
        $('#editModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var modal = $(this);

            modal.find('#document_id').val(button.data('document_id'));
            modal.find('#document_type_id').val(button.data('document_type_id'));
            modal.find('#resident_id').val(button.data('resident_id'));
            modal.find('#file_path').val(button.data('file_path'));
        });

        // SweetAlert feedbacks
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

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="post" action="function.php" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Edit Document</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="document_id" id="document_id">

                    <div class="form-group">
                        <label>Document Type</label>
                        <select name="document_type_id" id="document_type_id" class="form-control" required>
                            <?php
                            $res = mysqli_query($con, "SELECT * FROM document_types");
                            while ($row = mysqli_fetch_assoc($res)) {
                                echo "<option value='{$row['document_type_id']}'>{$row['type_name']}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Resident</label>
                        <select name="resident_id" id="resident_id" class="form-control" required>
                            <?php
                            $res = mysqli_query($con, "SELECT id, CONCAT(fname, ' ', lname) AS name FROM tblresident");
                            while ($row = mysqli_fetch_assoc($res)) {
                                echo "<option value='{$row['id']}'>{$row['name']}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Upload Document</label>
                        <input type="file" name="document_file" class="form-control" id="document_file">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="update_document" class="btn btn-primary">Update</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>
</body>
</html>
