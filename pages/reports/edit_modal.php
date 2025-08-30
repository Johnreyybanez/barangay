<?php
// Ensure $row is defined in the scope before including this file
?>
<div id="editModal<?php echo $row['report_id']; ?>" class="modal fade">
    <div class="modal-dialog">
        <form method="post" action="financial_reports.php" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Edit Financial Report</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="report_id" value="<?php echo $row['report_id']; ?>">
                    <div class="form-group">
                        <label>Type</label>
                        <input type="text" name="report_type" class="form-control" value="<?php echo $row['report_type']; ?>">
                    </div>
                    <div class="form-group">
                        <label>Amount</label>
                        <input type="text" name="amount" class="form-control" value="<?php echo $row['amount']; ?>">
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control"><?php echo $row['description']; ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Transaction Date</label>
                        <input type="date" name="transaction_date" class="form-control" value="<?php echo $row['transaction_date']; ?>">
                    </div>
                   
                    <div class="form-group">
                        <label>Document</label>
                        <?php if (!empty($row['document_path'])): ?>
                            <p>Current Document: <a href="<?php echo $row['document_path']; ?>" target="_blank">View</a></p>
                        <?php endif; ?>
                        <input type="file" name="document" class="form-control">
                        <small class="text-muted">Upload new file (PDF, DOC, DOCX, JPG, PNG)</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" name="edit_report" class="btn btn-success">Update</button>
                </div>
            </div>
        </form>
    </div>
</div>
