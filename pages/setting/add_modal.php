<!-- Add Modal -->
<div class="modal fade" id="addModal">
<div class="modal-dialog modal-sm" style="width:300px !important;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add Setting</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <form method="POST" action="function.php" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Upload Image:</label>
                        <input type="file" name="image" class="form-control-file" required>
                    </div>

                    <div class="form-group">
                        <label>Barangay:</label>
                        <input type="text" name="barangay" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>City:</label>
                        <input type="text" name="city" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Contact No.:</label>
                        <input type="text" name="contact_no" class="form-control" required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" name="add_setting" class="btn btn-success">Add Setting</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
