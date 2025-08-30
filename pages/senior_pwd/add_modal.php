<div id="addModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Add Senior PWD Service</h4>
            </div>
            <form method="post" action="function.php">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Resident:</label>
                        <select class="form-control" name="resident_id" required>
                            <?php
                            $res = mysqli_query($con, "SELECT id, CONCAT(fname, ' ', lname) AS name FROM tblresident");
                            while ($row = mysqli_fetch_assoc($res)) {
                                echo "<option value='{$row['id']}'>{$row['name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Service Type:</label>
                        <select class="form-control" name="service_type_id" required>
                            <?php
                            $res = mysqli_query($con, "SELECT service_type_id, type_name FROM service_types");
                            while ($row = mysqli_fetch_assoc($res)) {
                                echo "<option value='{$row['service_type_id']}'>{$row['type_name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Service Date:</label>
                        <input type="date" class="form-control" name="service_date" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="add_service" class="btn btn-primary">Add</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
