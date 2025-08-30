<?php
// Ensure $row is defined before including this file
if (isset($row)) {
?>
<div class="modal fade" id="editModal<?php echo $row['service_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel<?php echo $row['service_id']; ?>" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Edit Senior PWD Service</h4>
            </div>
            <form method="post" action="function.php">
                <div class="modal-body">
                    <input type="hidden" name="service_id" value="<?php echo $row['service_id']; ?>">

                    <div class="form-group">
                        <label>Resident</label>
                        <select class="form-control" name="resident_id" required>
                            <?php
                            // Fetch residents for the select dropdown
                            $residents = mysqli_query($con, "SELECT id, CONCAT(fname, ' ', lname) AS name FROM tblresident");
                            while ($resident = mysqli_fetch_assoc($residents)) {
                                $selected = ($resident['id'] == $row['resident_id']) ? 'selected' : '';
                                echo "<option value='{$resident['id']}' {$selected}>{$resident['name']}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Service Type</label>
                        <select class="form-control" name="service_type_id" required>
                            <?php
                            // Fetch service types for the select dropdown
                            $services = mysqli_query($con, "SELECT service_type_id, type_name FROM service_types");
                            while ($service = mysqli_fetch_assoc($services)) {
                                $selected = ($service['service_type_id'] == $row['service_type_id']) ? 'selected' : '';
                                echo "<option value='{$service['service_type_id']}' {$selected}>{$service['type_name']}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Service Date</label>
                        <input type="date" name="service_date" class="form-control" value="<?php echo $row['service_date']; ?>" required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" name="update_service" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php } ?>
