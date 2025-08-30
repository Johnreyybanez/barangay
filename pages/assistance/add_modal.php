<div class="modal fade" id="addModal">
    <div class="modal-dialog modal-s">
    <form method="POST" action="assistance_requests.php" enctype="multipart/form-data">

            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">Add Assistance Request</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body">
                    <!-- Resident ID (Dropdown) -->
                    <div class="form-group">
                        <label for="resident_id">Resident Name:</label>
                        <select id="resident_id" name="resident_id" class="form-control" required>
                            <option value="">-- Select Resident Name --</option>
                            <?php
                            $residentQuery = mysqli_query($con, "SELECT id, CONCAT(fname, ' ', mname, ' ', lname) AS resident_name FROM tblresident");
                            while ($resident = mysqli_fetch_assoc($residentQuery)) {
                                echo '<option value="' . $resident['id'] . '">' . $resident['resident_name'] . '</option>';
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
                            $query = mysqli_query($con, "SELECT * FROM service_types");
                            while ($service = mysqli_fetch_assoc($query)) {
                                echo '<option value="' . $service['service_type_id'] . '">' . $service['type_name'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <!-- Request Date -->
                    <div class="form-group">
                        <label for="request_date">Request Date:</label>
                        <input type="date" id="request_date" name="request_date" class="form-control" required>
                    </div>

                    <!-- Status Dropdown -->
                    <div class="form-group">
                        <label for="status">Status:</label>
                        <select id="status" name="status" class="form-control" required>
                            <option value="Pending">Pending</option>
                            <option value="Approved">Approved</option>
                            <option value="Rejected">Rejected</option>
                        </select>
                    </div>

                    <!-- File Upload (Required) -->
                    <div class="form-group">
                        <label for="document">Upload Document <span class="text-danger">*</span>:</label>
                        <input type="file" id="document" name="document" class="form-control-file" required>
                        <small class="text-muted">Only PDF, JPG, PNG, or DOC files allowed.</small>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button type="submit" name="add_request" class="btn btn-primary">Submit</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>
