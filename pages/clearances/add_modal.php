<!-- Add Barangay Clearance Modal -->
<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addModalLabel">Add Barangay Clearance</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" action="function.php" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="resident_id">Resident Name</label>
                        <select class="form-control" name="resident_id" required>
                            <option value="">Select Resident</option>
                            <?php
                            $residentQuery = "SELECT id, CONCAT(fname, ' ', lname) AS resident_name FROM tblresident";
                            $residentResult = mysqli_query($con, $residentQuery);
                            while ($resident = mysqli_fetch_assoc($residentResult)) {
                                echo "<option value='{$resident['id']}'>{$resident['resident_name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="clearance_type_id">Clearance Type</label>
                        <select class="form-control" name="clearance_type_id" required>
                            <option value="">Select Type</option>
                            <?php
                            $typeQuery = "SELECT clearance_type_id, type_name FROM clearance_types";
                            $typeResult = mysqli_query($con, $typeQuery);
                            while ($type = mysqli_fetch_assoc($typeResult)) {
                                echo "<option value='{$type['clearance_type_id']}'>{$type['type_name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="issued_by">Issued By</label>
                        <select class="form-control" name="issued_by" required>
                            <option value="">Select Official</option>
                            <?php
                            $officialQuery = "SELECT id, CONCAT(sPosition, ' ', completeName) AS official_name FROM tblofficial";
                            $officialResult = mysqli_query($con, $officialQuery);
                            while ($official = mysqli_fetch_assoc($officialResult)) {
                                echo "<option value='{$official['id']}'>{$official['official_name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="issue_date">Issue Date</label>
                        <input type="date" class="form-control" name="issue_date" required>
                    </div>
                    <div class="form-group">
                        <label for="document">Upload Document</label>
                        <input type="file" class="form-control" name="document" accept=".pdf,.jpg,.png,.doc,.docx" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success" name="add_clearance">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
