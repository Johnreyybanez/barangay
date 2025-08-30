<!-- Edit Service Type Modal -->
<div class="modal fade" id="editModal<?php echo $row['business_type_id']; ?>">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Business Type</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <form method="POST" action="function.php">
                <div class="modal-body">
                    <input type="hidden" name="business_type_id" value="<?php echo $row['business_type_id']; ?>">

                    <div class="form-group">
                        <label>Business Type Name:</label>
                        <input type="text" name="edit_business_type_name" class="form-control" value="<?php echo $row['business_type_name']; ?>" required>
                    </div>

                    
                </div>

                <div class="modal-footer">
                    <button type="submit" name="edit_service" class="btn btn-success">Save Changes</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
