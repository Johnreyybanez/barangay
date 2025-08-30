<?php
session_start();
if (!isset($_SESSION['role'])) {
    header("Location: ../../login.php");
    exit();
}
ob_start();
include('../head_css.php');
?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<body class="skin-black">
<?php include "../connection.php"; ?>
<?php include('../header.php'); ?>

<div class="wrapper row-offcanvas row-offcanvas-left">
    <?php include('../sidebar-left.php'); ?>
    <aside class="right-side">
        <section class="content-header">
            <h1>Sitio and Purok</h1>
        </section>

        <section class="content">
            <div class="row">
                <div class="box">
                    <div class="box-header">
                        <div style="padding:10px;">
                            <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addModal">
                                <i class="fa fa-plus"></i> Add Sitio & Purok
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="confirmDelete()">
                                <i class="fas fa-trash-alt"></i> Delete
                            </button>

                        </div>
                        <hr>
                    </div>

                    <div class="box-body table-responsive">
                        <form method="post">
                            <table id="table" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th style="width: 20px !important;">
                                            <input type="checkbox" class="cbxMain" onchange="checkMain(this)"/>
                                        </th>
                                        <th>Sitio Name</th>
                                        <th>Purok Name</th>
                                        <th style="width: 40px !important;">Option</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $query = mysqli_query($con, "SELECT * FROM sitio_purok");
                                    while ($row = mysqli_fetch_assoc($query)) {
                                        echo '
                                        <tr>
                                            <td><input type="checkbox" name="chk_delete[]" class="chk_delete" value="'.$row['id'].'" /></td>
                                            <td>'.$row['sitio'].'</td>
                                            <td>'.$row['purok'].'</td>
                                            <td>
                                                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#editModal'.$row['id'].'">
                                                    <i class="fa fa-pencil-square-o"></i> Edit
                                                </button>
                                            </td>
                                        </tr>
                                        <div class="modal fade" id="editModal'.$row['id'].'" tabindex="-1" role="dialog">
                                           <div class="modal-dialog modal-sm" style="width:300px !important;">
                                                <form method="POST">
                                                    <div class="modal-content">
                                                        <div class="modal-header"><h4 class="modal-title">Edit Sitio & Purok</h4></div>
                                                        <div class="modal-body">
                                                            <input type="hidden" name="id" value="'.$row['id'].'">
                                                            <label>Sitio:</label>
                                                            <input type="text" name="edit_sitio" class="form-control" value="'.$row['sitio'].'" required>
                                                            <label>Purok:</label>
                                                            <input type="text" name="edit_purok" class="form-control" value="'.$row['purok'].'" required>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                                            <button type="submit" name="edit_sitio_purok" class="btn btn-success">Update</button>
                                                            </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>';
                                    }
                                    ?>
                                </tbody>
                            </table>

                           
                        </form>
                    </div>
                </div>

                <!-- Add Modal -->
                <div class="modal fade" id="addModal" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-sm" style="width:300px !important;">
                        <form method="POST">
                            <div class="modal-content">
                                <div class="modal-header"><h4 class="modal-title">Add Sitio & Purok</h4></div>
                                <div class="modal-body">
                                    <label>Sitio:</label>
                                    <input type="text" name="sitio" class="form-control" required>
                                    <label>Purok:</label>
                                    <input type="text" name="purok" class="form-control" required>
                                </div>
                                <div class="modal-footer">                       
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                    <button type="submit" name="add_sitio_purok" class="btn btn-success">Save</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </section>
    </aside>
</div>
<?php include "../footer.php"; ?>

<script>
    $(function() {
        $("#table").dataTable({
            "aoColumnDefs": [{ "bSortable": false, "aTargets": [0, 3] }],
            "aaSorting": []
        });
    });

    function checkMain(checkbox) {
        var checkboxes = document.getElementsByClassName('chk_delete');
        for (var i = 0; i < checkboxes.length; i++) {
            checkboxes[i].checked = checkbox.checked;
        }
    }
</script>
<?php

include '../connection.php';

// 🔧 Notification Helper
function insertNotification($con, $user_id, $message, $icon = 'fa fa-info-circle') {
    if (!$user_id) return;
    $stmt = $con->prepare("INSERT INTO notifications (user_id, message, icon, status) VALUES (?, ?, ?, 'unread')");
    $stmt->bind_param("iss", $user_id, $message, $icon);
    $stmt->execute();
    $stmt->close();
}

// 🔐 Session data
$current_user_id = $_SESSION['id'] ?? $_SESSION['userid'] ?? 0;
$current_role = $_SESSION['role'] ?? 'Unknown';

// ✅ ADD
if (isset($_POST['add_sitio_purok'])) {
    $sitio = mysqli_real_escape_string($con, $_POST['sitio']);
    $purok = mysqli_real_escape_string($con, $_POST['purok']);

    $check = mysqli_query($con, "SELECT * FROM sitio_purok WHERE sitio='$sitio' AND purok='$purok'");

    if (mysqli_num_rows($check) > 0) {
        echo "<script>
            Swal.fire({ icon: 'error', title: 'Duplicate Entry', text: 'Sitio and Purok already exist!' }).then(() => {
                window.location = window.location.href;
            });
        </script>";
    } else {
        $insert = mysqli_query($con, "INSERT INTO sitio_purok (sitio, purok) VALUES ('$sitio', '$purok')");

        if ($insert) {
            $action = "Added Sitio: '$sitio', Purok: '$purok'";
            mysqli_query($con, "INSERT INTO tbllogs (user, logdate, action) VALUES ('$current_role', NOW(), '$action')");
            insertNotification($con, $current_user_id, $action, "fa fa-user-plus");

            echo "<script>
                Swal.fire({ icon: 'success', title: 'Added', text: 'Sitio and Purok added successfully!' }).then(() => {
                    window.location = window.location.href;
                });
            </script>";
        }
    }
}

// ✅ EDIT
if (isset($_POST['edit_sitio_purok'])) {
    $id = $_POST['id'];
    $sitio = mysqli_real_escape_string($con, $_POST['edit_sitio']);
    $purok = mysqli_real_escape_string($con, $_POST['edit_purok']);

    $update = mysqli_query($con, "UPDATE sitio_purok SET sitio='$sitio', purok='$purok' WHERE id='$id'");

    if ($update) {
        $action = "Edited Sitio/Purok ID $id to Sitio: '$sitio', Purok: '$purok'";
        mysqli_query($con, "INSERT INTO tbllogs (user, logdate, action) VALUES ('$current_role', NOW(), '$action')");
        insertNotification($con, $current_user_id, $action, "fa fa-edit");

        echo "<script>
            Swal.fire({ icon: 'success', title: 'Updated', text: 'Sitio and Purok updated successfully!' }).then(() => {
                window.location = window.location.href;
            });
        </script>";
    }
}
// ✅ DELETE
if (isset($_POST['btn_delete']) && isset($_POST['chk_delete'])) {
    $count = 0;
    foreach ($_POST['chk_delete'] as $id) {
        $id = intval($id);
        $result = mysqli_query($con, "SELECT sitio, purok FROM sitio_purok WHERE id='$id'");
        $row = mysqli_fetch_assoc($result);
        $sitio = $row['sitio'] ?? '';
        $purok = $row['purok'] ?? '';

        $delete = mysqli_query($con, "DELETE FROM sitio_purok WHERE id='$id'");
        if ($delete) {
            $count++;
            $action = "Deleted Sitio: '$sitio', Purok: '$purok' (ID: $id)";
            mysqli_query($con, "INSERT INTO tbllogs (user, logdate, action) VALUES ('$current_role', NOW(), '$action')");
        }
    }

    // 🔔 Add single notification showing the total
    if ($count > 0) {
        $summary = "Deleted $count Sitio and Purok entr" . ($count == 1 ? "y" : "ies");
        insertNotification($con, $current_user_id, $summary, "fa fa-trash");
    }

    echo "<script>
        Swal.fire({ icon: 'success', title: 'Deleted', text: 'Selected entries deleted!' }).then(() => {
            window.location = window.location.href;
        });
    </script>";
}

?>

<script>
    function confirmDelete() {
        const checked = document.querySelectorAll('.chk_delete:checked');
        if (checked.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'No selection',
                text: 'Please select at least one entry to delete.'
            });
            return;
        }

        Swal.fire({
            title: 'Are you sure?',
            text: 'You are about to delete selected Sitio & Purok!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.style.display = 'none';

                checked.forEach(cb => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'chk_delete[]';
                    input.value = cb.value;
                    form.appendChild(input);
                });

                const deleteInput = document.createElement('input');
                deleteInput.type = 'hidden';
                deleteInput.name = 'btn_delete';
                deleteInput.value = '1';
                form.appendChild(deleteInput);

                document.body.appendChild(form);
                form.submit();
            }
        });
    }
</script>

</body>
</html>
