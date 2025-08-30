<?php
// Ensure this is INSIDE a while loop with $row already defined

$edit_query = mysqli_query($con, "SELECT * FROM tblresident WHERE id = '".$row['id']."' ");
$erow = mysqli_fetch_array($edit_query);

echo '<div id="editModal'.$row['id'].'" class="modal fade">
<form class="form-horizontal" method="post" enctype="multipart/form-data">
  <div class="modal-dialog modal-l">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Edit Resident Information</h4>
        </div>
        <div class="modal-body">
            <input type="hidden" name="hidden_id" value="'.$erow['id'].'"/>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Last Name:</label>
                        <input name="txt_edit_lname" class="form-control" type="text" value="'.htmlspecialchars($erow['lname']).'"/>
                    </div>
                    <div class="form-group">
                        <label>First Name:</label>
                        <input name="txt_edit_fname" class="form-control" type="text" value="'.htmlspecialchars($erow['fname']).'"/>
                    </div>
                    <div class="form-group">
                        <label>Middle Name:</label>
                        <input name="txt_edit_mname" class="form-control" type="text" value="'.htmlspecialchars($erow['mname']).'"/>
                    </div>
                    <div class="form-group">
                        <label>Birthdate:</label>
                        <input name="txt_edit_bdate" class="form-control" type="date" value="'.$erow['bdate'].'"/>
                    </div>
                    <div class="form-group">
                        <label>Birthplace:</label>
                        <input name="txt_edit_bplace" class="form-control" type="text" value="'.htmlspecialchars($erow['bplace']).'"/>
                    </div>
                    <div class="form-group">
                        <label>Age:</label>
                        <input class="form-control" type="text" value="'.$erow['age'].'" disabled/>
                    </div>
                    <div class="form-group">
                        <label>Contact No:</label>
                        <input name="txt_edit_contact" class="form-control" type="text" value="'.htmlspecialchars($erow['contact_no']).'"/>
                    </div>
                    <div class="form-group">
                        <label>Sitio:</label>
                        <select name="txt_edit_sitio" class="form-control">';

                            $sitio_query = mysqli_query($con, "SELECT DISTINCT sitio FROM sitio_purok");
                            while ($sitio = mysqli_fetch_assoc($sitio_query)) {
                                $selected = ($erow['sitio'] == $sitio['sitio']) ? 'selected' : '';
                                echo '<option value="'.htmlspecialchars($sitio['sitio']).'" '.$selected.'>'.htmlspecialchars($sitio['sitio']).'</option>';
                            }
                        echo '</select>
                    </div>
                    <div class="form-group">
                        <label>Purok:</label>
                        <select name="txt_edit_purok" class="form-control">';

                            $purok_query = mysqli_query($con, "SELECT DISTINCT purok FROM sitio_purok");
                            while ($purok = mysqli_fetch_assoc($purok_query)) {
                                $selected = ($erow['purok'] == $purok['purok']) ? 'selected' : '';
                                echo '<option value="'.htmlspecialchars($purok['purok']).'" '.$selected.'>'.htmlspecialchars($purok['purok']).'</option>';
                            }
                        echo '</select>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label>Civil Status:</label>
                        <input name="txt_edit_cstatus" class="form-control" type="text" value="'.htmlspecialchars($erow['civilstatus']).'"/>
                    </div>
                    <div class="form-group">
                        <label>Address:</label>
                        <textarea name="txt_edit_address" class="form-control">'.htmlspecialchars($erow['address']).'</textarea>
                    </div>
                    <div class="form-group">
                        <label>Occupation:</label>
                        <input name="txt_edit_occp" class="form-control" type="text" value="'.htmlspecialchars($erow['occupation']).'"/>
                    </div>
                    <div class="form-group">
                        <label>Religion:</label>
                        <input name="txt_edit_religion" class="form-control" type="text" value="'.htmlspecialchars($erow['religion']).'"/>
                    </div>
                    <div class="form-group">
                        <label>Gender:</label>
                        <select name="ddl_edit_gender" class="form-control">
                            <option value="Male" '.($erow['gender'] == "Male" ? 'selected' : '').'>Male</option>
                            <option value="Female" '.($erow['gender'] == "Female" ? 'selected' : '').'>Female</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>PWD:</label>
                        <select name="ddl_edit_pwd" class="form-control">
                            <option value="Yes" '.($erow['pwd'] == "Yes" ? 'selected' : '').'>Yes</option>
                            <option value="No" '.($erow['pwd'] == "No" ? 'selected' : '').'>No</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Senior Citizen:</label>
                        <select name="ddl_edit_senior" class="form-control">
                            <option value="Yes" '.($erow['senior_citizen'] == "Yes" ? 'selected' : '').'>Yes</option>
                            <option value="No" '.($erow['senior_citizen'] == "No" ? 'selected' : '').'>No</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Image:</label>
                        <input name="txt_edit_image" class="form-control" type="file"/>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            <button type="submit" name="btn_save" class="btn btn-success">Update</button>
        </div>
    </div>
  </div>
</form> 
</div>';
?>
