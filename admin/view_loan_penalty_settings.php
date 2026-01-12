<?php
$base_path = '../';
$page_title = 'Loan Penalty Settings';
$current_page = 'admin';

require_once $base_path . 'helpers/connect.php';

include $base_path . 'includes/header.php';
?>

<div class="page-header">
    <h1>Loan Penalty Settings</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="index.php">Admin</a></li>
            <li class="breadcrumb-item active">Penalty Settings</li>
        </ol>
    </nav>
</div>

<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-gavel me-2"></i>Penalty Configuration</h5>
    </div>
    <div class="card-body">
        <form>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Grace Period (Days)</label>
                    <input type="number" class="form-control" value="3" min="0">
                    <small class="text-muted">Number of days after due date before penalty applies</small>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Penalty Type</label>
                    <select class="form-select">
                        <option value="percentage">Percentage of Amount Due</option>
                        <option value="fixed">Fixed Amount</option>
                        <option value="daily">Daily Rate</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Penalty Rate/Amount</label>
                    <input type="number" class="form-control" value="5" step="0.01" min="0">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Maximum Penalty</label>
                    <input type="number" class="form-control" placeholder="No limit" step="0.01" min="0">
                    <small class="text-muted">Leave empty for no maximum</small>
                </div>
            </div>
            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="autoApply" checked>
                    <label class="form-check-label" for="autoApply">Automatically apply penalties</label>
                </div>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Save Settings</button>
        </form>
    </div>
</div>

<?php include $base_path . 'includes/footer.php'; ?>
