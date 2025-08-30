<?php
session_start();
if (!isset($_SESSION['role'])) {
    header("Location: ../../login.php");
    exit();
} else {
    ob_start();
    include('../head_css.php'); 
?>
<body class="skin-black">
    <?php include "../connection.php"; ?>
    <?php include('../header.php'); ?>

    <div class="wrapper row-offcanvas row-offcanvas-left">
        <?php include('../sidebar-left.php'); ?>

        <aside class="right-side">
            <section class="content-header">
                <h1>Manage Settings</h1>
            </section>

            <section class="content">
                <div class="row">
                    <div class="box">
                        <div class="box-header">
                            <div style="padding:10px;">
                                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addModal">
                                    <i class="fa fa-plus"></i> Add Setting
                                </button>
                                <button class="btn btn-danger btn-sm" onclick="confirmDelete()">
                                    <i class="fas fa-trash-alt"></i> Delete
                                </button>
                            </div>
                              <hr>
                        </div>


                        <div class="box-body table-responsive">
                            <form method="post" id="deleteForm">
                                <table id="table" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th style="width: 20px !important;">
                                                <input type="checkbox" class="cbxMain" onchange="checkMain(this)"/>
                                            </th>
                                            <th>Image</th>
                                            <th>Barangay</th>
                                            <th>City</th>
                                            <th>Contact No</th>
                                            <th style="width: 40px !important;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $query = mysqli_query($con, "SELECT * FROM settings");
                                        while ($row = mysqli_fetch_assoc($query)) {
                                            echo '
                                            <tr>
                                                <td><input type="checkbox" name="chk_delete[]" class="chk_delete" value="'.$row['id'].'" /></td>
                                                <td><img src="uploads/'.$row['image'].'" width="50" height="50"></td>
                                                <td>'.$row['barangay'].'</td>
                                                <td>'.$row['city'].'</td>
                                                <td>'.$row['contact_no'].'</td>
                                                <td>
                                                    <button class="btn btn-primary btn-sm" data-target="#editModal'.$row['id'].'" data-toggle="modal">
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

    <?php include "../footer.php"; ?>

    <!-- ✅ SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    $(function() {
        $("#table").dataTable({
            "aoColumnDefs": [{ "bSortable": false, "aTargets": [0, 5] }],
            "aaSorting": []
        });
    });

    function checkMain(source) {
        let checkboxes = document.querySelectorAll('input[name="chk_delete[]"]:not(.cbxMain)');
        checkboxes.forEach(checkbox => checkbox.checked = source.checked);
    }

    function confirmDelete() {
        let checkedBoxes = document.querySelectorAll('input[name="chk_delete[]"]:checked:not(.cbxMain)');
        if (checkedBoxes.length === 0) {
            Swal.fire({
                title: 'No Selection!',
                text: 'Please select at least one setting to delete.',
                icon: 'warning',
                confirmButtonText: 'OK',
                confirmButtonColor: '#3085d6'
            });
            return;
        }

        Swal.fire({
            title: 'Are you sure?',
            text: `You are about to delete ${checkedBoxes.length} setting(s). This action cannot be undone!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('deleteForm');
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'btn_delete';
                hiddenInput.value = '1';
                form.appendChild(hiddenInput);
                form.submit();
            }
        });
    }

    // ✅ SweetAlert Notifications
    <?php if (isset($_SESSION['success_add'])): ?>
    Swal.fire({
        title: 'Success!',
        text: 'Setting added successfully!',
        icon: 'success',
        confirmButtonColor: '#28a745'
    });
    <?php unset($_SESSION['success_add']); endif; ?>

    <?php if (isset($_SESSION['success_edit'])): ?>
    Swal.fire({
        title: 'Success!',
        text: 'Setting updated successfully!',
        icon: 'success',
        confirmButtonColor: '#28a745'
    });
    <?php unset($_SESSION['success_edit']); endif; ?>

    <?php if (isset($_SESSION['delete']) && $_SESSION['delete'] == 1): ?>
    Swal.fire({
        title: 'Deleted!',
        text: 'Selected settings have been deleted.',
        icon: 'success',
        confirmButtonColor: '#28a745'
    });
    <?php unset($_SESSION['delete']); endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
    Swal.fire({
        title: 'Error!',
        text: '<?php echo addslashes($_SESSION["error"]); ?>',
        icon: 'error',
        confirmButtonColor: '#dc3545'
    });
    <?php unset($_SESSION['error']); endif; ?>
    </script>
</body>
</html>
<?php } ?>
