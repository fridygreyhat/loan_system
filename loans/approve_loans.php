<?php
$base_path = '../';
$page_title = 'Approve Loans';
$current_page = 'loans';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $loan_id = intval($_POST['loan_id'] ?? 0);
    $action = $_POST['action'];
    
    try {
        if ($action === 'approve') {
            $stmt = $con->prepare("UPDATE loans SET status = 'approved', release_date = CURRENT_DATE WHERE id = ?");
            $stmt->execute([$loan_id]);
            $message = 'Loan approved successfully';
        } elseif ($action === 'deny') {
            $stmt = $con->prepare("UPDATE loans SET status = 'denied' WHERE id = ?");
            $stmt->execute([$loan_id]);
            $message = 'Loan denied';
        }
    } catch (PDOException $e) {
        $error = 'Error updating loan status';
    }
}

$loans = [];
try {
    $stmt = $con->query("
        SELECT l.*, b.fname, b.lname, b.mobile, lp.name as product_name
        FROM loans l
        LEFT JOIN borrower b ON l.borrower_id = b.id
        LEFT JOIN loan_product lp ON l.loan_product_id = lp.id
        WHERE l.status = 'pending'
        ORDER BY l.id DESC
    ");
    $loans = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
}

include $base_path . 'includes/header.php';
?>

<div class="page-header">
    <div>
        <h1><i class="fas fa-check-double text-success me-2"></i><?php echo __('approve_loans'); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item"><a href="view_loans.php"><?php echo __('loans'); ?></a></li>
                <li class="breadcrumb-item active"><?php echo __('approve_loans'); ?></li>
            </ol>
        </nav>
    </div>
</div>

<?php if ($message): ?>
<div class="alert alert-success alert-dismissible fade show">
    <i class="fas fa-check-circle me-2"></i><?php echo $message; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if ($error): ?>
<div class="alert alert-danger alert-dismissible fade show">
    <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="stat-icon warning">
                <i class="fas fa-hourglass-half"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo count($loans); ?></h3>
                <p><?php echo __('pending_loans'); ?></p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-list me-2"></i><?php echo __('pending_loans'); ?></h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th><?php echo __('loan_number'); ?></th>
                        <th><?php echo __('borrower'); ?></th>
                        <th><?php echo __('phone'); ?></th>
                        <th><?php echo __('product'); ?></th>
                        <th><?php echo __('principal'); ?></th>
                        <th><?php echo __('interest'); ?></th>
                        <th><?php echo __('duration'); ?></th>
                        <th><?php echo __('actions'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($loans)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            <i class="fas fa-check-circle fa-3x mb-3 d-block text-success"></i>
                            <?php echo __('no_pending_loans'); ?>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($loans as $loan): ?>
                    <tr>
                        <td><strong>#<?php echo $loan['id']; ?></strong></td>
                        <td><?php echo htmlspecialchars(($loan['fname'] ?? '') . ' ' . ($loan['lname'] ?? '')); ?></td>
                        <td><?php echo htmlspecialchars($loan['mobile'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($loan['product_name'] ?? '-'); ?></td>
                        <td>TZS <?php echo number_format($loan['principal_amount'] ?? 0, 2); ?></td>
                        <td><?php echo $loan['loan_interest'] ?? 0; ?>%</td>
                        <td><?php echo $loan['loan_duration'] ?? 0; ?> <?php echo __('months'); ?></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="view_loan_details.php?id=<?php echo $loan['id']; ?>" class="btn btn-outline-primary" title="<?php echo __('view'); ?>">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="loan_id" value="<?php echo $loan['id']; ?>">
                                    <input type="hidden" name="action" value="approve">
                                    <button type="submit" class="btn btn-success" title="<?php echo __('approve'); ?>" onclick="return confirm('Approve this loan?')">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="loan_id" value="<?php echo $loan['id']; ?>">
                                    <input type="hidden" name="action" value="deny">
                                    <button type="submit" class="btn btn-danger" title="<?php echo __('deny'); ?>" onclick="return confirm('Deny this loan?')">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include $base_path . 'includes/footer.php'; ?>
