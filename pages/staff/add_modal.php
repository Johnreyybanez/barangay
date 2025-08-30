<!-- ========================= MODAL ======================= -->
<div id="addModal" class="modal fade">
  <form method="post" enctype="multipart/form-data">
    <div class="modal-dialog modal-sm" style="width:300px !important;">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title">Manage User</h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">

              <div class="form-group">
                <label>Full Name:</label>
                <input name="txt_name" class="form-control input-sm" type="text" placeholder="Lastname, Firstname Middlename" required />
              </div>

              <div class="form-group">
                <label>Username:</label>
                <input name="txt_uname" class="form-control input-sm" type="text" placeholder="Username" required />
                <label id="user_msg" style="color:#CC0000;"></label>
              </div>

              <div class="form-group">
                <label>Role:</label>
                <select name="txt_role" class="form-control input-sm" required>
                  <option value="">-- Select Role --</option>
                  <option value="Admin">Admin</option>
                  <option value="Clerk">Clerk</option>
                  <option value="Official">Official</option>
                </select>
              </div>

              <div class="form-group">
                <label>Password:</label>
                <input name="txt_pass" class="form-control input-sm" type="password" placeholder="Password" required />
              </div>

              <div class="form-group">
                <label>Profile Image:</label>
                <input name="txt_image" class="form-control input-sm" type="file" accept="image/*" />
                <small class="form-text text-muted">Upload a profile image (optional).</small>
              </div>

            </div>
          </div>
        </div>
        <div class="modal-footer">
          <input type="button" class="btn btn-default btn-sm" data-dismiss="modal" value="Cancel" />
          <input type="submit" class="btn btn-success btn-sm" name="btn_add" id="btn_add" value="Save" />
        </div>
      </div>
    </div>
  </form>
</div>
