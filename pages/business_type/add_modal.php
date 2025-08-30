<div class="modal fade" id="addModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add Business Type</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form method="post" action="function.php">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="business_type_name">Business Type Name</label>
                        <input type="text" class="form-control" name="business_type_name" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="add_business_type" class="btn btn-success">Save</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
