<?php
session_start();
include '../connection.php'; // Ensure connection is included

if (!isset($_SESSION['role'])) {
    header("Location: ../../login.php");
    exit();
}

// DELETE FUNCTION
if (isset($_POST['btn_delete'])) {
    if (!empty($_POST['chk_delete'])) {
        $blotter_ids = implode(",", array_map('intval', $_POST['chk_delete']));
        $delete_query = mysqli_query($con, "DELETE FROM blotter_records WHERE blotter_id IN ($blotter_ids)");
        if ($delete_query) {
            $_SESSION['delete'] = 1;
            header("Location: " . $_SERVER['REQUEST_URI']);
            exit();
        } else {
            echo "<script>alert('Error deleting records.');</script>";
        }
    } else {
        echo "<script>alert('No records selected for deletion.');</script>";
    }
}

// UPDATE FUNCTION (for Solve Modal)
if (isset($_POST['blotter_id'], $_POST['status'], $_POST['resolution'])) {
    $blotter_id = intval($_POST['blotter_id']);
    $status = mysqli_real_escape_string($con, $_POST['status']);
    $resolution = mysqli_real_escape_string($con, $_POST['resolution']);
    $update_query = "UPDATE blotter_records SET status = '$status', resolution = '$resolution' WHERE blotter_id = $blotter_id";
    if (mysqli_query($con, $update_query)) {
        $_SESSION['success_edit'] = 1;
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    } else {
        $_SESSION['update_error'] = 1;
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }
}

include('../head_css.php');
?>
<body class="skin-black">
    <?php include "../header.php"; ?>
    <div class="wrapper row-offcanvas row-offcanvas-left">
        <?php include('../sidebar-left.php'); ?>
        <aside class="right-side">
            <section class="content-header">
                <h1>Blotter Records</h1>
            </section>
            <section class="content">
                <div class="row">
                    <div class="box">
                        <div class="box-header">
                            <div style="padding:10px;">
                                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addBlotterModal">
                                    <i class="fa fa-plus"></i> Add Blotter
                                </button>
                                <button class="btn btn-danger btn-sm" onclick="confirmDelete()">
                                    <i class="fas fa-trash-alt"></i> Delete
                                </button>
                            </div>
                            <hr>
                        </div>
                        <div class="box-body table-responsive">
                            <form method="post" id="deleteForm">
                                <table id="blotterTable" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th style="width: 20px;">
                                                <input type="checkbox" class="cbxMain" onchange="checkMain(this)" />
                                            </th>
                                            <th>Complainant</th>
                                            <th>Respondent</th>
                                            <th>Incident Date</th>
                                            <th>Status</th>
                                            <th>Incident Description</th>
                                            <th>Resolution</th>
                                            <th style="width: 250px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $query = mysqli_query($con, "
                                            SELECT 
                                                b.blotter_id, b.incident_date, b.status, 
                                                b.incident_desc, b.resolution,
                                                c.lname AS complainant_lname, c.fname AS complainant_fname,
                                                r.lname AS respondent_lname, r.fname AS respondent_fname,
                                                c.id AS complainant_id, r.id AS respondent_id
                                            FROM blotter_records AS b
                                            LEFT JOIN tblresident AS c ON b.complainant_id = c.id
                                            LEFT JOIN tblresident AS r ON b.respondent_id = r.id
                                        ");
                                        while ($row = mysqli_fetch_assoc($query)) {
                                            // Determine status class and text
                                            $status_class = $row['status'] == 'Pending' ? 'status-pending' : 'status-solved';
                                            $status_text = ucfirst($row['status']);
                                            ?>
                                            <tr>
                                                <td><input type="checkbox" name="chk_delete[]" class="chk_delete" value="<?php echo $row['blotter_id']; ?>" /></td>
                                                <td><?php echo $row['complainant_lname'] . ', ' . $row['complainant_fname']; ?></td>
                                                <td><?php echo $row['respondent_lname'] . ', ' . $row['respondent_fname']; ?></td>
                                                <td><?php echo $row['incident_date']; ?></td>
                                                <td><span class="<?php echo $status_class; ?>"><?php echo $status_text; ?></span></td>
                                                <td><?php echo htmlspecialchars($row['incident_desc']); ?></td>
                                                <td><?php echo htmlspecialchars($row['resolution']); ?></td>
                                                <td>
                                                    <button class="btn btn-success btn-sm" data-target="#solveModal<?php echo $row['blotter_id']; ?>" data-toggle="modal">
                                                        <i class="fa fa-check-circle"></i> Solve
                                                    </button>
                                                    <button class="btn btn-primary btn-sm" data-target="#editModal<?php echo $row['blotter_id']; ?>" data-toggle="modal">
                                                        <i class="fa fa-pencil-square-o"></i> Edit
                                                    </button>
                                                    <a href="form.php?blotter_id=<?php echo $row['blotter_id']; ?>" class="btn btn-info btn-sm">
                                                        <i class="fa fa-file-text"></i> Generate
                                                    </a>
                                                </td>
                                            </tr>
                                            <!-- Solve Modal -->
                                            <div id="solveModal<?php echo $row['blotter_id']; ?>" class="modal fade" role="dialog">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                            <h4 class="modal-title">Update Blotter Resolution</h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <form action="" method="POST">
                                                                <input type="hidden" name="blotter_id" value="<?php echo $row['blotter_id']; ?>" />
                                                                <div class="form-group">
                                                                    <label for="status">Status:</label>
                                                                    <select name="status" id="status" class="form-control" required>
                                                                        <option value="Pending" <?php echo $row['status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                                                        <option value="Solved" <?php echo $row['status'] == 'Solved' ? 'selected' : ''; ?>>Solved</option>
                                                                    </select>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="resolution">Resolution:</label>
                                                                    <textarea name="resolution" id="resolution" class="form-control" rows="4" required><?php echo htmlspecialchars($row['resolution']); ?></textarea>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                                    <button type="submit" class="btn btn-success">Update</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Edit Modal -->
                                            <div class="modal fade" id="editModal<?php echo $row['blotter_id']; ?>">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title">Edit Blotter Record</h4>
                                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                        </div>
                                                        <form method="POST" action="function.php">
                                                            <div class="modal-body">
                                                                <input type="hidden" name="blotter_id" value="<?php echo $row['blotter_id']; ?>">
                                                                <!-- Complainant ID Select -->
                                                                <div class="form-group">
                                                                    <label>Complainant:</label>
                                                                    <select name="edit_complainant_id" class="form-control" required>
                                                                        <option value="">Select Complainant</option>
                                                                        <?php
                                                                        $q_complainant = mysqli_query($con, "SELECT * FROM tblresident ORDER BY lname ASC");
                                                                        while ($complainant = mysqli_fetch_array($q_complainant)) {
                                                                            $selected = ($complainant['id'] == $row['complainant_id']) ? 'selected' : '';
                                                                            echo '<option value="' . $complainant['id'] . '" ' . $selected . '>' . $complainant['lname'] . ', ' . $complainant['fname'] . ' ' . $complainant['mname'] . '</option>';
                                                                        }
                                                                        ?>
                                                                    </select>
                                                                </div>
                                                                <!-- Respondent ID Select -->
                                                                <div class="form-group">
                                                                    <label>Respondent:</label>
                                                                    <select name="edit_respondent_id" class="form-control" required>
                                                                        <option value="">Select Respondent</option>
                                                                        <?php
                                                                        $q_respondent = mysqli_query($con, "SELECT * FROM tblresident ORDER BY lname ASC");
                                                                        while ($respondent = mysqli_fetch_array($q_respondent)) {
                                                                            $selected = ($respondent['id'] == $row['respondent_id']) ? 'selected' : '';
                                                                            echo '<option value="' . $respondent['id'] . '" ' . $selected . '>' . $respondent['lname'] . ', ' . $respondent['fname'] . ' ' . $respondent['mname'] . '</option>';
                                                                        }
                                                                        ?>
                                                                    </select>
                                                                </div>
                                                                <!-- Incident Date -->
                                                                <div class="form-group">
                                                                    <label>Incident Date:</label>
                                                                    <input type="datetime-local" name="edit_incident_date" class="form-control" value="<?php echo date('Y-m-d\TH:i', strtotime($row['incident_date'])); ?>" required>
                                                                </div>
                                                                <!-- Incident Description -->
                                                                <div class="form-group">
                                                                    <label>Incident Description:</label>
                                                                    <textarea name="edit_incident_desc" class="form-control" required><?php echo htmlspecialchars($row['incident_desc']); ?></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                <button type="submit" name="edit_blotter" class="btn btn-success">Update</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>
                                <input type="hidden" name="btn_delete">
                            </form>
                        </div>
                    </div>
                    <?php include "../edit_notif.php"; ?>
                    <?php include "../added_notif.php"; ?>
                    <?php include "../delete_notif.php"; ?>
                    <?php include "../duplicate_error.php"; ?>
                    <?php include "add_modal.php"; ?>
                    <?php include "function.php"; ?>
                </div>
            </section>
        </aside>
    </div>

    <!-- CSS for Status Colors -->
    <style>
        .status-pending {
            background-color: #f0ad4e;
            color: white;
            padding: 2px 6px;
            border-radius: 4px;
            display: inline-block;
        }
        .status-solved {
            background-color: #5cb85c;
            color: white;
            padding: 2px 6px;
            border-radius: 4px;
            display: inline-block;
        }
    </style>

    <?php if (isset($_SESSION['success_add'])): ?>
        <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire('Success!', 'Blotter record added successfully!', 'success');
        });
        </script>
        <?php unset($_SESSION['success_add']); endif; ?>
        <?php if (isset($_SESSION['success_edit'])): ?>
        <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire('Updated!', 'Blotter record updated successfully!', 'success');
        });
        </script>
        <?php unset($_SESSION['success_edit']); endif; ?>
        <?php if (isset($_SESSION['delete'])): ?>
        <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire('Deleted!', 'Blotter records deleted successfully.', 'success');
        });
        </script>
        <?php unset($_SESSION['delete']); endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
        <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire('Error!', '<?php echo addslashes($_SESSION['error']); ?>', 'error');
        });
        </script>
        <?php unset($_SESSION['error']); endif; ?>
    <?php include "../footer.php"; ?>
    <script type="text/javascript">
        $(function() {
            $("#blotterTable").dataTable({
                "aoColumnDefs": [{ "bSortable": false, "aTargets": [0, 7] }],
                "aaSorting": []
            });
        });
        function checkMain(checkbox) {
            $(".chk_delete").prop("checked", $(checkbox).prop("checked"));
        }
        function confirmDelete() {
            if ($(".chk_delete:checked").length > 0) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You are about to delete the selected record(s). This action cannot be undone!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $("#deleteForm").submit();
                    }
                });
            } else {
                Swal.fire({
                    icon: 'info',
                    title: 'No selection',
                    text: 'Please select at least one record to delete.'
                });
            }
        }
    </script>
</body>
</html>