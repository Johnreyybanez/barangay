<?php
// Ensure $row is defined before including this file
if (isset($row)) {
?>
<div class="modal fade" id="editModal<?php echo $row['business_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel<?php echo $row['business_id']; ?>" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Edit Business Registration</h4>
            </div>
            <form method="post" action="function.php">
                <div class="modal-body">
                    <input type="hidden" name="business_id" value="<?php echo $row['business_id']; ?>">

                    <div class="form-group">
                        <label>Business Name</label>
                        <input type="text" name="business_name" class="form-control" value="<?php echo $row['business_name']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Business Type</label>
                        <input type="text" name="business_type" class="form-control" value="<?php echo $row['business_type']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Registration Date</label>
                        <input type="date" name="registration_date" class="form-control" value="<?php echo $row['registration_date']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Validity Period</label>
                        <input type="date" name="validity_period" class="form-control" value="<?php echo $row['validity_period']; ?>" required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" name="update_business" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php } ?>
