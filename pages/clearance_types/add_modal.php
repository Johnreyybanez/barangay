<!-- Add Modal -->
<div class="modal fade" id="addModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add Clearance Type</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <form method="POST" action="function.php">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Clearance Type Name:</label>
                        <input type="text" name="type_name" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Description:</label>
                        <textarea name="description" class="form-control" required></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" name="add_clearance" class="btn btn-success">Add Clearance Type</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
