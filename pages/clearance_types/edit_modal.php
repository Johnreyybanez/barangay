<!-- Edit Clearance Type Modal -->
<div class="modal fade" id="editModal<?php echo $row['clearance_type_id']; ?>">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Clearance Type</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <form method="POST" action="function.php">
                <div class="modal-body">
                    <input type="hidden" name="clearance_type_id" value="<?php echo $row['clearance_type_id']; ?>">

                    <div class="form-group">
                        <label>Clearance Type Name:</label>
                        <input type="text" name="edit_type_name" class="form-control" value="<?php echo $row['type_name']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Description:</label>
                        <textarea name="edit_description" class="form-control" required><?php echo $row['description']; ?></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" name="edit_clearance" class="btn btn-success">Save Changes</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
