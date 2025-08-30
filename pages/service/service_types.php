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
                <h1>Service Types</h1>
            </section>

            <section class="content">
                <div class="row">
                    <div class="box">
                        <div class="box-header">
                            <div style="padding:10px;">
                                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addModal">
                                    <i class="fa fa-plus"></i> Add Service 
                                </button>
                                <!-- DELETE uses JS confirmation -->
                                <button class="btn btn-danger btn-sm" onclick="confirmDelete()">
                                    <i class="fas fa-trash-alt"></i> Delete
                                </button>
                            </div>
                              <hr>
                        </div>

                        <div class="box-body table-responsive">
                            <form method="post" id="deleteForm" action="function.php">
                                <table id="table" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th style="width: 20px !important;">
                                                <input type="checkbox" class="cbxMain" onchange="checkMain(this)"/>
                                            </th>
                                            <th>Type Name</th>
                                            <th>Description</th>
                                            <th style="width: 40px !important;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $query = mysqli_query($con, "SELECT * FROM service_types");
                                        while ($row = mysqli_fetch_assoc($query)) {
                                            echo '
                                            <tr>
                                                <td><input type="checkbox" name="chk_delete[]" class="chk_delete" value="'.$row['service_type_id'].'" /></td>
                                                <td>'.$row['type_name'].'</td>
                                                <td>'.$row['description'].'</td>
                                                <td>
                                                    <button class="btn btn-primary btn-sm" data-target="#editModal'.$row['service_type_id'].'" data-toggle="modal">
                                                        <i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit
                                                    </button>
                                                </td>
                                            </tr>';
                                            include "edit_modal.php";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                                <!-- Hidden delete trigger -->
                                <input type="hidden" name="btn_delete" value="1">
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

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(function () {
            $("#table").dataTable({
                "aoColumnDefs": [{ "bSortable": false, "aTargets": [0, 3] }],
                "aaSorting": []
            });
        });

        function checkMain(source) {
            const checkboxes = document.querySelectorAll('.chk_delete');
            checkboxes.forEach(cb => cb.checked = source.checked);
        }

        function confirmDelete() {
            const selected = document.querySelectorAll('.chk_delete:checked');
            if (selected.length === 0) {
                Swal.fire({
                    icon: 'info',
                    title: 'No Selection',
                    text: 'Please select at least one service type to delete.',
                    confirmButtonColor: '#3085d6'
                });
            } else {
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'Selected service type(s) will be permanently deleted.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('deleteForm').submit();
                    }
                });
            }
        }

        // SweetAlert session feedbacks
        <?php if (isset($_SESSION['success_add'])): ?>
        Swal.fire({
            icon: 'success',
            title: 'Added!',
            text: 'Service type added successfully.',
            confirmButtonColor: '#28a745'
        });
        <?php unset($_SESSION['success_add']); endif; ?>

        <?php if (isset($_SESSION['success_edit'])): ?>
        Swal.fire({
            icon: 'success',
            title: 'Updated!',
            text: 'Service type updated successfully.',
            confirmButtonColor: '#28a745'
        });
        <?php unset($_SESSION['success_edit']); endif; ?>

        <?php if (isset($_SESSION['delete'])): ?>
        Swal.fire({
            icon: 'success',
            title: 'Deleted!',
            text: 'Selected service types have been deleted.',
            confirmButtonColor: '#28a745'
        });
        <?php unset($_SESSION['delete']); endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: '<?php echo addslashes($_SESSION["error"]); ?>',
            confirmButtonColor: '#dc3545'
        });
        <?php unset($_SESSION['error']); endif; ?>
    </script>
</body>
</html>
<?php } ?>
