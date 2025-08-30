<div id="addModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Add Document</h4>
            </div>
            <form method="post" action="function.php" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Document Type:</label>
                        <select class="form-control" name="document_type_id" required>
                            <?php
                            // Fetch document types
                            $res = mysqli_query($con, "SELECT document_type_id, type_name FROM document_types");
                            while ($row = mysqli_fetch_assoc($res)) {
                                echo "<option value='{$row['document_type_id']}'>{$row['type_name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Resident:</label>
                        <select class="form-control" name="resident_id" required>
                            <?php
                            // Fetch residents
                            $res = mysqli_query($con, "SELECT id, CONCAT(fname, ' ', lname) AS name FROM tblresident");
                            while ($row = mysqli_fetch_assoc($res)) {
                                echo "<option value='{$row['id']}'>{$row['name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Upload Document:</label>
                        <input type="file" class="form-control" name="document_file" accept="application/pdf,image/*" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="add_document" class="btn btn-primary">Add</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
