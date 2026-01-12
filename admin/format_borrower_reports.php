<?php
$base_path = '../';
$page_title = 'Borrower Report Format';
$current_page = 'admin';

require_once $base_path . 'helpers/connect.php';

include $base_path . 'includes/header.php';
?>

<div class="page-header">
    <h1>Borrower Report Format</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="index.php">Admin</a></li>
            <li class="breadcrumb-item active">Report Format</li>
        </ol>
    </nav>
</div>

<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-file-alt me-2"></i>Configure Report Columns</h5>
    </div>
    <div class="card-body">
        <p class="text-muted mb-4">Select which fields to include in borrower reports:</p>
        <form>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="col_name" checked>
                        <label class="form-check-label" for="col_name">Full Name</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="col_email" checked>
                        <label class="form-check-label" for="col_email">Email</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="col_phone" checked>
                        <label class="form-check-label" for="col_phone">Phone</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="col_address">
                        <label class="form-check-label" for="col_address">Address</label>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="col_loans" checked>
                        <label class="form-check-label" for="col_loans">Active Loans</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="col_balance" checked>
                        <label class="form-check-label" for="col_balance">Outstanding Balance</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="col_status" checked>
                        <label class="form-check-label" for="col_status">Status</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="col_group">
                        <label class="form-check-label" for="col_group">Group</label>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="col_joined">
                        <label class="form-check-label" for="col_joined">Date Joined</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="col_id">
                        <label class="form-check-label" for="col_id">Borrower ID</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="col_branch">
                        <label class="form-check-label" for="col_branch">Branch</label>
                    </div>
                </div>
            </div>
            <hr>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Save Format</button>
        </form>
    </div>
</div>

<?php include $base_path . 'includes/footer.php'; ?>
