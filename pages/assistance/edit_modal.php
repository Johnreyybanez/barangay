<div class="modal fade" id="editModal<?php echo htmlspecialchars($row['request_id']); ?>" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <form method="POST" action="function.php" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Edit Assistance Request</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="request_id" value="<?php echo htmlspecialchars($row['request_id']); ?>">

                    <!-- Resident Name (Dropdown or Input) -->
                    <div class="form-group">
                        <label for="resident_id">Resident Name:</label>
                        <select id="resident_id" name="resident_id" class="form-control" required>
                            <option value="">-- Select Resident --</option>
                            <?php
                            $residentQuery = mysqli_query($con, "SELECT id, CONCAT(fname, ' ', mname, ' ', lname) AS resident_name FROM tblresident");
                            while ($resident = mysqli_fetch_assoc($residentQuery)) {
                                $selected = ($resident['id'] == $row['resident_id']) ? 'selected' : '';
                                echo '<option value="' . htmlspecialchars($resident['id']) . '" ' . $selected . '>' . htmlspecialchars($resident['resident_name']) . '</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <!-- Service Type Dropdown -->
                    <div class="form-group">
                        <label for="service_type_id">Service Type:</label>
                        <select id="service_type_id" name="service_type_id" class="form-control" required>
                            <option value="">-- Select Service Type --</option>
                            <?php
                            $serviceQuery = mysqli_query($con, "SELECT * FROM service_types");
                            while ($service = mysqli_fetch_assoc($serviceQuery)) {
                                $selected = ($service['service_type_id'] == $row['service_type_id']) ? 'selected' : '';
                                echo '<option value="' . htmlspecialchars($service['service_type_id']) . '" ' . $selected . '>' . htmlspecialchars($service['type_name']) . '</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <!-- Request Date -->
                    <div class="form-group">
                        <label for="request_date">Request Date:</label>
                        <input type="date" id="request_date" name="request_date" class="form-control" value="<?php echo htmlspecialchars($row['request_date']); ?>" required>
                    </div>

                    <!-- Status -->
                    <div class="form-group">
                        <label for="status">Status:</label>
                        <select id="status" name="status" class="form-control" required>
                            <option value="Pending" <?php echo ($row['status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                            <option value="Approved" <?php echo ($row['status'] == 'Approved') ? 'selected' : ''; ?>>Approved</option>
                            <option value="Rejected" <?php echo ($row['status'] == 'Rejected') ? 'selected' : ''; ?>>Rejected</option>
                        </select>
                    </div>

                    <!-- File Upload (Optional) -->
                    <div class="form-group">
                        <label for="document">Upload New Document (Optional):</label>
                        <input type="file" id="document" name="document" class="form-control-file">
                        <?php if (!empty($row['document_path'])) { ?>
                            <p>Current File: <a href="<?php echo htmlspecialchars($row['document_path']); ?>" target="_blank">View</a></p>
                        <?php } ?>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" name="update_request" class="btn btn-primary">Update</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>
