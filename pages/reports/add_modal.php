<!-- Add Modal -->
<div id="addModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Add Financial Report</h4>
            </div>
            <form method="post" action="functions.php" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Report Type:</label>
                        <select class="form-control" name="report_type" required>
                            <option value="Income">Income</option>
                            <option value="Expense">Expense</option>
                            <option value="Budget">Budget</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Amount:</label>
                        <input type="number" step="0.01" class="form-control" name="amount" required>
                    </div>
                   
                    <div class="form-group">
                        <label>Description:</label>
                        <textarea class="form-control" name="description" required></textarea>
                    </div>
                    <div class="form-group">
                        <label>Transaction Date:</label>
                        <input type="date" class="form-control" name="transaction_date" required>
                    </div>
                    <div class="form-group">
                        <label>Upload Document:</label>
                        <input type="file" class="form-control" name="document">
                    </div>
                </div>
                <div class="modal-footer">                    
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" name="add_report" class="btn btn-success">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
