<?php
$base_path = '../';
$page_title = 'Approve Repayments';
$current_page = 'repayments';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $repayment_id = intval($_POST['repayment_id'] ?? 0);
    $action = $_POST['action'];
    
    try {
        if ($action === 'approve') {
            $stmt = $con->prepare("UPDATE repayments SET status = 'approved' WHERE id = ?");
            $stmt->execute([$repayment_id]);
            $message = 'Repayment approved successfully';
        } elseif ($action === 'reject') {
            $stmt = $con->prepare("UPDATE repayments SET status = 'rejected' WHERE id = ?");
            $stmt->execute([$repayment_id]);
            $message = 'Repayment rejected';
        }
    } catch (PDOException $e) {
        $error = 'Error updating repayment status';
    }
}

$repayments = [];
try {
    $stmt = $con->query("
        SELECT r.*, l.id as loan_id, b.fname, b.lname
        FROM repayments r
        LEFT JOIN loans l ON r.loan_id = l.id
        LEFT JOIN borrower b ON l.borrower_id = b.id
        WHERE r.status = 'pending' OR r.status IS NULL
        ORDER BY r.collection_date DESC
    ");
    $repayments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
}

include $base_path . 'includes/header.php';
?>

<div class="page-header">
    <div>
        <h1><i class="fas fa-check-double text-success me-2"></i><?php echo __('approve_repayments'); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item"><a href="view_repayments.php"><?php echo __('repayments'); ?></a></li>
                <li class="breadcrumb-item active"><?php echo __('approve_repayments'); ?></li>
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
                <h3><?php echo count($repayments); ?></h3>
                <p><?php echo __('pending'); ?></p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-list me-2"></i><?php echo __('pending_repayments'); ?></h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th><?php echo __('loan'); ?></th>
                        <th><?php echo __('borrower'); ?></th>
                        <th><?php echo __('amount'); ?></th>
                        <th><?php echo __('date'); ?></th>
                        <th><?php echo __('payment_method'); ?></th>
                        <th><?php echo __('actions'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($repayments)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="fas fa-check-circle fa-3x mb-3 d-block text-success"></i>
                            <?php echo __('no_pending_repayments'); ?>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($repayments as $r): ?>
                    <tr>
                        <td><?php echo $r['id']; ?></td>
                        <td>#<?php echo $r['loan_id']; ?></td>
                        <td><?php echo htmlspecialchars(($r['fname'] ?? '') . ' ' . ($r['lname'] ?? '')); ?></td>
                        <td class="text-success fw-bold">TZS <?php echo number_format($r['amount'] ?? 0, 2); ?></td>
                        <td><?php echo date('M d, Y', strtotime($r['collection_date'])); ?></td>
                        <td><?php echo htmlspecialchars($r['payment_method'] ?? 'Cash'); ?></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="repayment_id" value="<?php echo $r['id']; ?>">
                                    <input type="hidden" name="action" value="approve">
                                    <button type="submit" class="btn btn-success" onclick="return confirm('Approve this repayment?')">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="repayment_id" value="<?php echo $r['id']; ?>">
                                    <input type="hidden" name="action" value="reject">
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Reject this repayment?')">
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
