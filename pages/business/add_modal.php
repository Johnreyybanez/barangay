<div id="addModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Add Business Registration</h4>
            </div>
            <form method="post" action="function.php">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Business Name:</label>
                        <input type="text" class="form-control" name="business_name" required>
                    </div>
                    <div class="form-group">
                        <label>Owner:</label>
                        <select class="form-control" name="owner_id" required>
                            <?php
                            $res = mysqli_query($con, "SELECT id, CONCAT(fname, ' ', lname) AS name FROM tblresident");
                            while ($row = mysqli_fetch_assoc($res)) {
                                echo "<option value='{$row['id']}'>{$row['name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Business Type:</label>
                        <input type="text" class="form-control" name="business_type" required>
                    </div>
                    <div class="form-group">
                        <label>Registration Date:</label>
                        <input type="date" class="form-control" name="registration_date" required>
                    </div>
                    <div class="form-group">
                        <label>Validity Period:</label>
                        <input type="date" class="form-control" name="validity_period" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="add_business" class="btn btn-primary">Add</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
