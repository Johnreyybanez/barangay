<!-- ========================= MODAL ======================= -->
<div id="addResidentModal" class="modal fade">
    <form class="form-horizontal" method="post" enctype="multipart/form-data">
        <div class="modal-dialog modal-l">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title text-center">Manage Residents</h4>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row justify-content-center">
                            <!-- RIGHT COLUMN FIRST -->
                            <div class="col-md-5 col-sm-12">
                            <div class="form-group">
                                    <label class="control-label">Last Name:</label>
                                    <input name="txt_lname" class="form-control input-sm" type="text" placeholder="Last Name" required/>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">First Name:</label>
                                    <input name="txt_fname" class="form-control input-sm" type="text" placeholder="First Name" required/>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Middle Name:</label>
                                    <input name="txt_mname" class="form-control input-sm" type="text" placeholder="Middle Name"/>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Birthdate:</label>
                                    <input name="txt_bdate" class="form-control input-sm" type="date" required/>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Birthplace:</label>
                                    <input name="txt_bplace" class="form-control input-sm" type="text" placeholder="Birthplace" required/>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Age:</label>
                                    <input name="txt_age" class="form-control input-sm" type="number" min="1" placeholder="Age" required/>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Contact No:</label>
                                    <input name="txt_contact" class="form-control input-sm" type="text" placeholder="Contact No" required/>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Civil Status:</label>
                                    <input name="txt_cstatus" class="form-control input-sm" type="text" placeholder="Civil Status" required/>
                                </div>

                                <!-- Sitio -->
                                <div class="form-group">
                                    <label class="control-label">Sitio:</label>
                                    <select name="txt_sitio" class="form-control" required>
                                        <option value="">Select Sitio</option>
                                        <?php
                                        $sitioQuery = mysqli_query($con, "SELECT * FROM sitio_purok");
                                        while ($sitio = mysqli_fetch_assoc($sitioQuery)) {
                                            if (!empty($sitio['sitio'])) {
                                                echo '<option value="' . $sitio['sitio'] . '">' . $sitio['sitio'] . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                              
                            </div>

                            <div class="col-md-2"></div> <!-- Spacer -->

                            <!-- LEFT COLUMN (was originally on the left, now on the right) -->
                            <div class="col-md-5 col-sm-12">
                                  <!-- Purok -->
                                  <div class="form-group">
                                    <label class="control-label">Purok:</label>
                                    <select name="txt_purok" class="form-control" required>
                                        <option value="">Select Purok</option>
                                        <?php
                                        $purokQuery = mysqli_query($con, "SELECT * FROM sitio_purok");
                                        while ($purok = mysqli_fetch_assoc($purokQuery)) {
                                            if (!empty($purok['purok'])) {
                                                echo '<option value="' . $purok['purok'] . '">' . $purok['purok'] . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            <div class="form-group">
                                    <label class="control-label">Religion:</label>
                                    <input name="txt_religion" class="form-control input-sm" type="text" placeholder="Religion" required/>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Occupation:</label>
                                    <input name="txt_occp" class="form-control input-sm" type="text" placeholder="Occupation"/>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Address:</label>
                                    <textarea name="txt_address" class="form-control input-sm" placeholder="Address" required></textarea>
                                </div>
                            <div class="form-group">
                                    <label class="control-label">Gender:</label>
                                    <select name="ddl_gender" class="form-control input-sm" required>
                                        <option selected disabled>-Select Gender-</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">PWD:</label>
                                    <select name="ddl_pwd" class="form-control input-sm" required>
                                        <option selected disabled>-Select PWD Status-</option>
                                        <option value="Yes">Yes</option>
                                        <option value="No">No</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Senior Citizen:</label>
                                    <select name="ddl_senior" class="form-control input-sm" required>
                                        <option selected disabled>-Select Senior Status-</option>
                                        <option value="Yes">Yes</option>
                                        <option value="No">No</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Image:</label>
                                    <input name="txt_image" class="form-control input-sm" type="file" required/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-center">
                    <input type="button" class="btn btn-default btn-sm" data-dismiss="modal" value="Cancel"/>
                    <input type="submit" class="btn btn-success btn-sm" name="btn_add" id="btn_add" value="Add Resident"/>
                </div>
            </div>
        </div>
    </form>
</div>
