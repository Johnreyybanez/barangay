<?php echo '
<div id="editModal'.$row['id'].'" class="modal fade">
<form method="post" enctype="multipart/form-data">
  <div class="modal-dialog modal-sm" style="width:300px !important;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Edit User Info</h4>
        </div>
        <div class="modal-body">
        <div class="row">
            <div class="col-md-12">
                <input type="hidden" value="'.$row['id'].'" name="hidden_id" id="hidden_id"/>
                
                <div class="form-group">
                    <label>Name: <span style="color:gray; font-size: 10px;">(Lastname, Firstname Middlename)</span></label>
                    <input name="txt_edit_name" class="form-control input-sm" type="text" value="'.htmlspecialchars($row['fullname']).'" required/>
                </div>

                <div class="form-group">
                    <label>Username: </label>
                    <input name="txt_edit_uname" class="form-control input-sm" type="text" value="'.htmlspecialchars($row['username']).'" required/>
                </div>

                <div class="form-group">
                    <label>New Password (leave blank to keep current):</label>
                    <input name="txt_edit_pass" class="form-control input-sm" type="password" placeholder="Enter new password"/>
                </div>

                <div class="form-group">
                    <label>Role:</label>
                    <select name="txt_edit_role" class="form-control input-sm" required>
                        <option value="Admin"'.($row['role'] == 'Admin' ? ' selected' : '').'>Admin</option>
                        <option value="Clerk"'.($row['role'] == 'Clerk' ? ' selected' : '').'>Clerk</option>
                        <option value="Official"'.($row['role'] == 'Official' ? ' selected' : '').'>Official</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Profile Image:</label>
                    <input name="txt_edit_image" class="form-control input-sm" type="file" accept="image/*" />
                    <small class="form-text text-muted">Upload a new profile image (optional).</small>
                </div>

            </div>
        </div>
        </div>
        <div class="modal-footer">
            <input type="button" class="btn btn-default btn-sm" data-dismiss="modal" value="Cancel"/>
            <input type="submit" class="btn btn-success btn-sm" name="btn_save" value="Update"/>
        </div>
    </div>
  </div>
</form>
</div>'; ?>
