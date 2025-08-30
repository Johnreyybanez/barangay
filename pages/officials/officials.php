<!DOCTYPE html>
<html>

    <?php
    session_start();
    if(!isset($_SESSION['role']))
    {
        header("Location: ../../login.php"); 
    }
    else
    {
    ob_start();
    include('../head_css.php'); ?>
    
    <body class="skin-black">
        <!-- header logo: style can be found in header.less -->
        <?php 
        
        include "../connection.php";
        ?>
        <?php include('../header.php'); ?>

        <div class="wrapper row-offcanvas row-offcanvas-left">
            <!-- Left side column. contains the logo and sidebar -->
            <?php include('../sidebar-left.php'); ?>

            <!-- Right side column. Contains the navbar and content of the page -->
            <aside class="right-side">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        Barangay Officials
                    </h1>
                    
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="row">
                        <!-- left column -->
                            <div class="box">
                                <div class="box-header">
                                    <div style="padding:10px;">
                                        
                                        <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addCourseModal"><i class="fa fa-user-plus" aria-hidden="true"></i> Add Officials</button>  

                                        <?php 
                                            if(!isset($_SESSION['staff']))
                                            {
                                        ?>
                                        <button class="btn btn-danger btn-sm" onclick="confirmDelete()"><i class="fas fa-trash-alt" aria-hidden="true"></i> Delete</button> 
                                        <?php
                                            }
                                        ?>
                                
                                    </div>     
                                      <hr>                           
                                </div><!-- /.box-header -->
                                <div class="box-body table-responsive">
                                <form method="post" id="deleteForm">
                                    <table id="table" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <?php 
                                                    if(!isset($_SESSION['staff']))
                                                    {
                                                ?>
                                                <th style="width: 20px !important;"><input type="checkbox" name="chk_delete[]" class="cbxMain" onchange="checkMain(this)"/></th>
                                                <?php
                                                    }
                                                ?>
                                                <th>Position</th>
                                                <th>Name</th>
                                                <th>Contact</th>
                                                <th>Address</th>
                                                <th>Start of Term</th>
                                                <th>End of Term</th>
                                                <th style="width: 130px !important;">Option</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                if(!isset($_SESSION['staff']))
                                                {

                                                    $squery = mysqli_query($con, "select * from tblofficial ");
                                                    while($row = mysqli_fetch_array($squery))
                                                    {
                                                        echo '
                                                        <tr>
                                                            <td><input type="checkbox" name="chk_delete[]" class="chk_delete" value="'.$row['id'].'" /></td>
                                                            <td>'.$row['sPosition'].'</td>
                                                            <td>'.$row['completeName'].'</td>
                                                            <td>'.$row['pcontact'].'</td>
                                                            <td>'.$row['paddress'].'</td>
                                                            <td>'.$row['termStart'].'</td>
                                                            <td>'.$row['termEnd'].'</td>
                                                            <td>
                                                                <button class="btn btn-primary btn-sm" data-target="#editModal'.$row['id'].'" data-toggle="modal"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</button>';
                                                                if($row['status'] == 'Ongoing Term'){
                                                                    echo '<button type="button" class="btn btn-danger btn-sm" onclick="confirmEnd(\''.$row['id'].'\', \''.htmlspecialchars($row['completeName'], ENT_QUOTES).'\')">
                                                                            <i class="fa fa-minus-circle" aria-hidden="true"></i> End
                                                                        </button>';                                                           
                                                                }
                                                                else{
                                                                    echo '<button type="button" class="btn btn-success btn-sm" onclick="confirmStart(\''.$row['id'].'\', \''.htmlspecialchars($row['completeName'], ENT_QUOTES).'\')">
                                                                    <i class="fa fa-play-circle" aria-hidden="true"></i> Start
                                                                  </button>';
                                                            
                                                                }
                                                            echo '</td>
                                                        
                                                        </tr>
                                                        ';

                                                        include "edit_modal.php";
                                                       
                                                    }

                                                }
                                                else{
                                                    $squery = mysqli_query($con, "select * from tblofficial where status = 'Ongoing Term' group by termend");
                                                    while($row = mysqli_fetch_array($squery))
                                                    {
                                                        echo '
                                                        <tr>
                                                            <td>'.$row['sPosition'].'</td>
                                                            <td>'.$row['completeName'].'</td>
                                                            <td>'.$row['pcontact'].'</td>
                                                            <td>'.$row['paddress'].'</td>
                                                            <td>'.$row['termStart'].'</td>
                                                            <td>'.$row['termEnd'].'</td>
                                                            <td><button class="btn btn-primary btn-sm" data-target="#editModal'.$row['id'].'" data-toggle="modal"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</button></td>
                                                        </tr>
                                                        ';

                                                        include "edit_modal.php";
                                                    }
                                                }
                                            ?>
                                        </tbody>
                                    </table>

                                    <?php if (isset($_SESSION['end'])): ?>
                                    <script>
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Term ended successfully!',  
                                        position: 'center',
                                        showConfirmButton: true,
                                        timerProgressBar: true
                                    });
                                    </script>
                                    <?php unset($_SESSION['end']); ?>
                                    <?php endif; ?>
                                    <?php if (isset($_SESSION['start'])): ?>
                                    <script>
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Term started successfully!', 
                                        position: 'center',  
                                        showConfirmButton: true
                                    });
                                    </script>
                                    <?php unset($_SESSION['start']); ?>
                                    <?php endif; ?>

                                    <?php if (isset($_SESSION['duplicate'])): ?>
                                    <script>
                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'Cannot Start Term',
                                        text: 'Another official already has an ongoing term for this position.',
                                        confirmButtonColor: '#d33'
                                    });
                                    </script>
                                    <?php unset($_SESSION['duplicate']); ?>
                                    <?php endif; ?>

                                    <?php if (isset($_SESSION['error'])): ?>
                                    <script>
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: '<?php echo addslashes($_SESSION['error']); ?>'
                                    });
                                    </script>
                                    <?php unset($_SESSION['error']); ?>
                                    <?php endif; ?>

                                    </form>
                                </div><!-- /.box-body -->
                            </div><!-- /.box -->

                            <?php include "../duplicate_error.php"; ?>
                            <?php include "../edit_notif.php"; ?>

                            <?php include "../added_notif.php"; ?>

                            <?php include "../delete_notif.php"; ?>

            <?php include "add_modal.php"; ?>

            <?php include "function.php"; ?>


                    </div>   <!-- /.row -->
                </section><!-- /.content -->
            </aside><!-- /.right-side -->
        </div><!-- ./wrapper -->
        <!-- jQuery 2.0.2 -->
        <?php }
        include "../footer.php"; ?>
<script type="text/javascript">
    <?php
    if(!isset($_SESSION['staff']))
    {
    ?>
        $(function() {
            $("#table").dataTable({
               "aoColumnDefs": [ { "bSortable": false, "aTargets": [ 0,7 ] } ],"aaSorting": []
            });
        });
    <?php
    }
    else{
    ?>
        $(function() {
            $("#table").dataTable({
               "aoColumnDefs": [ { "bSortable": false, "aTargets": [ 6 ] } ],"aaSorting": []
            });
        });
    <?php
    }
    ?>
</script>
<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Hidden Form for 'End Term' -->
<form id="endForm" method="post">
    <input type="hidden" name="hidden_id" id="end_hidden_id">
    <input type="hidden" name="btn_end" value="1">
</form>

<script>
function confirmEnd(id, name) {
    event.preventDefault(); // prevent default action, if any

    Swal.fire({
        title: 'End Term?',
        text: `Are you sure you want to end the term of ${name}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, End it',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('end_hidden_id').value = id;
            document.getElementById('endForm').submit();
        }
    });
}

</script>
<!-- Hidden Form for 'Start Term' -->
<form id="startForm" method="post">
    <input type="hidden" name="hidden_id" id="start_hidden_id">
    <input type="hidden" name="btn_start" value="1">
</form>

<script>
function confirmStart(id, name) {
    Swal.fire({
        title: 'Start Term?',
        text: `Are you sure you want to start the term of ${name}?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Start it',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('start_hidden_id').value = id;
            document.getElementById('startForm').submit();
        }
    });
}
</script>

<!-- Delete Confirmation Script -->
<script>
function confirmDelete() {
    // Get all checked checkboxes
    const checkedBoxes = document.querySelectorAll('input[name="chk_delete[]"]:checked');
    
    if (checkedBoxes.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'No Selection',
            text: 'Please select at least one official to delete.',
            confirmButtonColor: '#3085d6'
        });
        return;
    }

    // Get names of selected officials for confirmation
    let officialNames = [];
    checkedBoxes.forEach(function(checkbox) {
        const row = checkbox.closest('tr');
        const nameCell = row.cells[2]; // Name is in the 3rd column (index 2)
        officialNames.push(nameCell.textContent.trim());
    });

    const confirmText = officialNames.length === 1 
        ? `Are you sure you want to delete ${officialNames[0]}?`
        : `Are you sure you want to delete these ${officialNames.length} officials?\n\n${officialNames.join('\n')}`;

    Swal.fire({
        title: 'Delete Officials?',
        text: confirmText,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Delete',
        cancelButtonText: 'Cancel',
        preConfirm: () => {
            return true;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Create a hidden input for the delete action
            const form = document.getElementById('deleteForm');
            const deleteInput = document.createElement('input');
            deleteInput.type = 'hidden';
            deleteInput.name = 'btn_delete';
            deleteInput.value = '1';
            form.appendChild(deleteInput);
            
            // Submit the form
            form.submit();
        }
    });
}

// Helper function for main checkbox
function checkMain(mainCheckbox) {
    const checkboxes = document.querySelectorAll('input[name="chk_delete[]"].chk_delete');
    checkboxes.forEach(function(checkbox) {
        checkbox.checked = mainCheckbox.checked;
    });
}
</script>

    </body>
</html>