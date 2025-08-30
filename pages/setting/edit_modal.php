<!-- Edit Setting Modal -->
<div class="modal fade" id="editModal<?php echo $row['id']; ?>">
    <div class="modal-dialog modal-sm" style="width:300px !important;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Setting</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <form method="POST" action="function.php" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="setting_id" value="<?php echo $row['id']; ?>">

                    <div class="form-group">
                        <label>Current Image:</label><br>
                        <?php if (!empty($row['image'])): ?>
                            <img src="uploads/<?php echo $row['image']; ?>" 
                                 alt="Current Image" class="img-thumbnail mb-2" width="100">
                        <?php else: ?>
                            <p>No image uploaded.</p>
                        <?php endif; ?>

                        <label>Change Image (optional):</label>
                        <input type="file" name="edit_image" class="form-control-file">
                    </div>

                    <div class="form-group">
                        <label>Barangay:</label>
                        <input type="text" name="edit_barangay" class="form-control"
                               value="<?php echo htmlspecialchars($row['barangay']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>City:</label>
                        <input type="text" name="edit_city" class="form-control"
                               value="<?php echo htmlspecialchars($row['city']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Contact No.:</label>
                        <input type="text" name="edit_contact_no" class="form-control"
                               value="<?php echo htmlspecialchars($row['contact_no']); ?>" required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" name="edit_setting" class="btn btn-success">Save Changes</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
