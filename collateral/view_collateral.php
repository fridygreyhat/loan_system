<?php
$base_path = '../';
$page_title = 'View Collateral';
$current_page = 'collateral';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$id = (int)($_GET['id'] ?? 0);
$item = null;
$error = '';

if ($id > 0) {
    try {
        $stmt = $con->prepare("
            SELECT c.*, b.fname, b.lname, b.mobile_no, b.unique_no,
                   l.loan_no, l.principal_amount, l.status AS loan_status, l.release_date
            FROM collateral c
            LEFT JOIN borrower b ON c.borrower_id = b.id
            LEFT JOIN loans l ON c.loan_id = l.id
            WHERE c.id = ?
        ");
        $stmt->execute([$id]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    } catch (PDOException $e) {
        error_log('view_collateral: ' . $e->getMessage());
        $error = 'Could not load the collateral: database error.';
    }
}

$status_badges = ['held' => 'primary', 'released' => 'success', 'seized' => 'danger', 'sold' => 'secondary'];

include $base_path . 'includes/header.php';
?>

<div class="content-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h1><i class="fas fa-shield-alt text-primary me-2"></i>Collateral Details</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item"><a href="collateral_register.php"><?php echo __('collateral_register'); ?></a></li>
                <li class="breadcrumb-item active">Collateral Details</li>
            </ol>
        </nav>
    </div>
    <?php if ($item): ?>
    <div class="d-flex gap-2">
        <a href="edit_collateral.php?id=<?php echo (int)$item['id']; ?>" class="btn btn-primary"><i class="fas fa-edit me-2"></i>Edit</a>
        <a href="collateral_register.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Back</a>
    </div>
    <?php endif; ?>
</div>

<?php if ($error): ?>
<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?></div>
<?php elseif (!$item): ?>
<div class="alert alert-warning">
    <i class="fas fa-exclamation-triangle me-2"></i>Collateral not found.
    <a href="collateral_register.php" class="alert-link">Back to the collateral register</a>.
</div>
<?php else: ?>
<?php $badge = $status_badges[$item['status']] ?? 'secondary'; ?>
<div class="row">
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><?php echo htmlspecialchars($item['name']); ?></h5>
                <span class="badge bg-<?php echo $badge; ?>"><?php echo htmlspecialchars(ucfirst($item['status'])); ?></span>
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <th style="width: 35%;">Type</th>
                        <td><?php echo htmlspecialchars($item['type'] ?: '-'); ?></td>
                    </tr>
                    <tr>
                        <th>Value</th>
                        <td><strong>TZS <?php echo number_format((float)$item['value'], 2); ?></strong></td>
                    </tr>
                    <tr>
                        <th>Serial / Registration No.</th>
                        <td><?php echo htmlspecialchars($item['serial_number'] ?: '-'); ?></td>
                    </tr>
                    <tr>
                        <th>Description</th>
                        <td><?php echo $item['description'] !== null && $item['description'] !== '' ? nl2br(htmlspecialchars($item['description'])) : '-'; ?></td>
                    </tr>
                    <tr>
                        <th>Recorded On</th>
                        <td><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($item['created_at']))); ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header"><h5 class="mb-0"><i class="fas fa-user me-2"></i>Borrower</h5></div>
            <div class="card-body">
                <?php if ($item['borrower_id'] && $item['fname'] !== null): ?>
                <p class="mb-1">
                    <a href="<?php echo $base_path; ?>borrowers/view_borrower.php?id=<?php echo (int)$item['borrower_id']; ?>">
                        <strong><?php echo htmlspecialchars(trim($item['fname'] . ' ' . $item['lname'])); ?></strong>
                    </a>
                </p>
                <?php if ($item['unique_no']): ?><p class="mb-1 text-muted small">ID: <?php echo htmlspecialchars($item['unique_no']); ?></p><?php endif; ?>
                <?php if ($item['mobile_no']): ?><p class="mb-0 text-muted small"><i class="fas fa-phone me-1"></i><?php echo htmlspecialchars($item['mobile_no']); ?></p><?php endif; ?>
                <?php else: ?>
                <p class="text-muted mb-0">No borrower linked.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h5 class="mb-0"><i class="fas fa-hand-holding-usd me-2"></i>Loan</h5></div>
            <div class="card-body">
                <?php if ($item['loan_id'] && $item['principal_amount'] !== null): ?>
                <p class="mb-1">
                    <a href="<?php echo $base_path; ?>loans/view_loan_details.php?id=<?php echo (int)$item['loan_id']; ?>">
                        <strong><?php echo htmlspecialchars($item['loan_no'] ?: '#' . $item['loan_id']); ?></strong>
                    </a>
                </p>
                <p class="mb-1 small">Principal: TZS <?php echo number_format((float)$item['principal_amount'], 2); ?></p>
                <?php if ($item['release_date']): ?><p class="mb-1 small">Released: <?php echo htmlspecialchars(date('d/m/Y', strtotime($item['release_date']))); ?></p><?php endif; ?>
                <?php if ($item['loan_status']): ?><p class="mb-0 small">Status: <?php echo htmlspecialchars($item['loan_status']); ?></p><?php endif; ?>
                <?php else: ?>
                <p class="text-muted mb-0">Not linked to a specific loan.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php include $base_path . 'includes/footer.php'; ?>
