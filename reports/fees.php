<?php
$base_path = '../';
$page_title = 'Fees Report';
$current_page = 'reports';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$fees = [];
$start_date = $_GET['start'] ?? date('Y-m-01');
$end_date = $_GET['end'] ?? date('Y-m-d');

try {
    $stmt = $con->prepare("
        SELECT lac.id, lac.amount, lac.creation_date AS created_at, l.id AS loan_id,
               b.fname, b.lname, lc.name AS fee_type
        FROM loan_applied_charges lac
        LEFT JOIN loans l ON lac.loan_id = l.id
        LEFT JOIN borrower b ON l.borrower_id = b.id
        LEFT JOIN loan_charges lc ON lac.loan_charge_id = lc.id
        WHERE lac.creation_date >= ? AND lac.creation_date < (?::date + 1)
        ORDER BY lac.creation_date DESC
    ");
    $stmt->execute([$start_date, $end_date]);
    $fees = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
}

$total_fees = array_sum(array_column($fees, 'amount'));

include $base_path . 'includes/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><i class="fas fa-receipt me-2"></i><?php echo __('fees_report'); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item"><a href="index.php"><?php echo __('reports'); ?></a></li>
                <li class="breadcrumb-item active"><?php echo __('fees'); ?></li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary"><i class="fas fa-print me-2"></i><?php echo __('print'); ?></button>
        <button class="btn btn-outline-secondary"><i class="fas fa-download me-2"></i><?php echo __('export'); ?></button>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label"><?php echo __('from_date'); ?></label>
                <input type="date" name="start" class="form-control" value="<?php echo $start_date; ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label"><?php echo __('to_date'); ?></label>
                <input type="date" name="end" class="form-control" value="<?php echo $end_date; ?>">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary"><i class="fas fa-filter me-2"></i><?php echo __('filter'); ?></button>
            </div>
        </form>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="stat-icon success">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="stat-info">
                <h3>TZS <?php echo number_format($total_fees, 2); ?></h3>
                <p><?php echo __('total_fees_collected'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="stat-icon primary">
                <i class="fas fa-receipt"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo count($fees); ?></h3>
                <p><?php echo __('fee_transactions'); ?></p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-list me-2"></i><?php echo __('fees_collected'); ?></h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th><?php echo __('date'); ?></th>
                        <th><?php echo __('loan_number'); ?></th>
                        <th><?php echo __('borrower'); ?></th>
                        <th><?php echo __('fee_type'); ?></th>
                        <th><?php echo __('amount'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($fees)): ?>
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">
                            <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                            <?php echo __('no_fees_found'); ?>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($fees as $item): ?>
                    <tr>
                        <td><?php echo date('M d, Y', strtotime($item['created_at'])); ?></td>
                        <td><strong>#<?php echo $item['loan_id']; ?></strong></td>
                        <td><?php echo htmlspecialchars(($item['fname'] ?? '') . ' ' . ($item['lname'] ?? '')); ?></td>
                        <td><?php echo htmlspecialchars($item['fee_type'] ?? '-'); ?></td>
                        <td class="text-success"><strong>TZS <?php echo number_format($item['amount'] ?? 0, 2); ?></strong></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include $base_path . 'includes/footer.php'; ?>
