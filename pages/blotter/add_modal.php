<!-- Add Blotter Modal -->
<div class="modal fade" id="addBlotterModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"> 
                <h4 class="modal-title">Add Blotter Record</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <form method="POST" action="function.php">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Complainant:</label>
                        <select name="complainant_id" class="form-control select2" required>
                            <option value="" disabled selected>Select Complainant</option>
                            <?php
                                $q_complainant = mysqli_query($con, "SELECT * FROM tblresident ORDER BY lname ASC");
                                while ($row = mysqli_fetch_array($q_complainant)) {
                                    echo '<option value="'.$row['id'].'">'.$row['lname'].', '.$row['fname'].' '.$row['mname'].'</option>';
                                }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Respondent:</label>
                        <select name="respondent_id" class="form-control select2" required>
                            <option value="" disabled selected>Select Respondent</option>
                            <?php
                                $q_respondent = mysqli_query($con, "SELECT * FROM tblresident ORDER BY lname ASC");
                                while ($row = mysqli_fetch_array($q_respondent)) {
                                    echo '<option value="'.$row['id'].'">'.$row['lname'].', '.$row['fname'].' '.$row['mname'].'</option>';
                                }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Incident Date:</label>
                        <input type="datetime-local" name="incident_date" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Incident Description:</label>
                        <textarea name="incident_desc" class="form-control" required></textarea>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" name="add_blotter" class="btn btn-success">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
