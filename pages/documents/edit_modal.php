<?php
// Ensure $row is defined before including this file
if (isset($row)) {
?>
<div class="modal fade" id="editModal<?php echo $row['document_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel<?php echo $row['document_id']; ?>" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Edit Document</h4>
            </div>
            <form method="post" action="function.php" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="document_id" value="<?php echo $row['document_id']; ?>">

                    <!-- Document Type Dropdown -->
                    <div class="form-group">
                        <label>Document Type:</label>
                        <select class="form-control" name="document_type_id" required>
                            <?php
                            // Fetch document types from the database
                            $res = mysqli_query($con, "SELECT document_type_id, type_name FROM document_types");
                            while ($type_row = mysqli_fetch_assoc($res)) {
                                $selected = ($type_row['document_type_id'] == $row['document_type_id']) ? 'selected' : '';
                                echo "<option value='{$type_row['document_type_id']}' {$selected}>{$type_row['type_name']}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <!-- Resident Dropdown -->
                    <div class="form-group">
                        <label>Resident:</label>
                        <select class="form-control" name="resident_id" required>
                            <?php
                            // Fetch residents from the database
                            $res = mysqli_query($con, "SELECT id, CONCAT(fname, ' ', lname) AS name FROM tblresident");
                            while ($resident_row = mysqli_fetch_assoc($res)) {
                                $selected = ($resident_row['id'] == $row['resident_id']) ? 'selected' : '';
                                echo "<option value='{$resident_row['id']}' {$selected}>{$resident_row['name']}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <!-- File Upload Input (Optional) -->
                    <div class="form-group">
                        <label>Upload New Document (optional):</label>
                        <input type="file" class="form-control" name="document_file" accept="application/pdf,image/*">
                    </div>

                    <!-- Existing Document File -->
                    <div class="form-group">
                        <label>Current Document:</label>
                        <p><?php echo $row['file_path']; ?></p>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" name="update_document" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php } ?>
