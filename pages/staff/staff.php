<!DOCTYPE html>
<html>

<?php
session_start();
if (!isset($_SESSION['role'])) {
    header("Location: ../../login.php");
} else {
    ob_start();
    include('../head_css.php'); 
?>
<body class="skin-black">

<?php 
include "../connection.php";
include('../header.php'); 
?>

<div class="wrapper row-offcanvas row-offcanvas-left">
    <?php include('../sidebar-left.php'); ?>

    <aside class="right-side">
        <section class="content-header">
            <h1>Manage Users</h1>
        </section>

        <section class="content">
            <div class="row">
                <div class="box">
                    <div class="box-header">
                        <div style="padding:10px;">
                            <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addModal">
                                <i class="fa fa-user-plus" aria-hidden="true"></i> Add User
                            </button>  
                            <button class="btn btn-danger btn-sm" onclick="confirmDelete()">
                                <i class="fas fa-trash-alt" aria-hidden="true"></i> Delete
                            </button> 
                        </div>        
                        <hr>                        
                    </div>
                    <div class="box-body table-responsive">
                        <form method="post" id="userForm">
                        <table id="table" class="table table-bordered table-striped">
    <thead>
        <tr>
            <th style="width: 20px !important;"><input type="checkbox" class="cbxMain" onchange="checkMain(this)"/></th>
            <th>Image</th>
            <th>Name</th>
            <th>Role</th>
            <th style="width: 40px !important;">Option</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $squery = mysqli_query($con, "SELECT * FROM tbluser");
        while ($row = mysqli_fetch_array($squery)) {
            // Set a default image if none exists
            $imagePath = !empty($row['image']) ? $row['image'] : '../../img/default_user.png'; // Path to default image
            echo '
            <tr>
                <td><input type="checkbox" name="chk_delete[]" class="chk_delete" value="'.$row['id'].'" /></td>
                <td>
                    <img src="'.htmlspecialchars($imagePath).'" alt="User Image" style="height: 40px; width: 40px; object-fit: cover; border-radius: 50%;" />
                </td>
                <td>'.$row['fullname'].'</td>
                <td>'.$row['role'].'</td>
                <td>
                    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#editModal'.$row['id'].'">
                        <i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit
                    </button>
                </td>
            </tr>';
            include "edit_modal.php";
        }
        ?>
    </tbody>
</table>

                        </form>
                    </div>
                </div>

                <?php include "add_modal.php"; ?>
                <?php include "function.php"; ?>

            </div>
        </section>
    </aside>
</div>

<?php } include "../footer.php"; ?>

<!-- DataTables -->
<script type="text/javascript">
$(function () {
    $("#table").dataTable({
        "aoColumnDefs": [{ "bSortable": false, "aTargets": [0, 3] }],
        "aaSorting": []
    });
});

function checkMain(cb) {
    $('.chk_delete').prop('checked', cb.checked);
}

function confirmDelete() {
    const checked = $('.chk_delete:checked').length;
    if (checked === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'No selection',
            text: 'Please select at least one user to delete.'
        });
        return;
    }

    Swal.fire({
        title: 'Are you sure?',
        text: "Selected user(s) will be permanently deleted!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $('<input>').attr({
                type: 'hidden',
                name: 'btn_delete',
                value: '1'
            }).appendTo('#userForm');
            $('#userForm').submit();
        }
    });
}
</script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- SweetAlert2 Notifications -->
<?php if (isset($_SESSION['added'])): ?>
<script>
Swal.fire({
    icon: 'success',
    title: 'User Added',
    text: 'The new user has been successfully added.'
});
</script>
<?php unset($_SESSION['added']); endif; ?>

<?php if (isset($_SESSION['edited'])): ?>
<script>
Swal.fire({
    icon: 'success',
    title: 'User Updated',
    text: 'User details have been updated successfully.'
});
</script>
<?php unset($_SESSION['edited']); endif; ?>

<?php if (isset($_SESSION['delete'])): ?>
<script>
Swal.fire({
    icon: 'success',
    title: 'User Deleted',
    text: 'Selected user(s) have been deleted.'
});
</script>
<?php unset($_SESSION['delete']); endif; ?>

<?php if (isset($_SESSION['duplicateuser'])): ?>
<script>
Swal.fire({
    icon: 'error',
    title: 'Duplicate Username',
    text: 'This username is already taken. Please choose another.'
});
</script>
<?php unset($_SESSION['duplicateuser']); endif; ?>

</body>
</html>
