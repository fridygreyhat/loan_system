<?php
$base_path = '../';
$page_title = 'Staff Details';
$current_page = 'staff';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$id = (int)($_GET['id'] ?? 0);
$member = null;
$loans = [];
$error = '';
$loan_limit = 200;
$loan_total = 0;
$portfolio_total = 0.0;

if ($id > 0) {
    try {
        $stmt = $con->prepare("
            SELECT s.id, s.fname, s.lname, s.email, s.phone_no, s.status, s.gender, s.address, s.city,
                   s.created_at, s.role_id, s.branch_id,
                   srt.name AS role_name, br.name AS branch_name
            FROM staff s
            LEFT JOIN staff_role_types srt ON s.role_id = srt.id
            LEFT JOIN branches br ON s.branch_id = br.id
            WHERE s.id = ?
        ");
        $stmt->execute([$id]);
        $member = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;

        if ($member) {
            $stmt = $con->prepare("
                SELECT COUNT(*), COALESCE(SUM(principal_amount), 0)
                FROM loans WHERE loan_officer_id = ? OR collector_id = ?
            ");
            $stmt->execute([$id, $id]);
            [$loan_total, $portfolio_total] = $stmt->fetch(PDO::FETCH_NUM);
            $loan_total = (int)$loan_total;
            $portfolio_total = (float)$portfolio_total;

            $stmt = $con->prepare("
                SELECT l.id, l.loan_no, l.principal_amount, l.release_date, l.maturity_date, l.status,
                       l.loan_officer_id, l.collector_id, b.fname, b.lname
                FROM loans l
                LEFT JOIN borrower b ON l.borrower_id = b.id
                WHERE l.loan_officer_id = ? OR l.collector_id = ?
                ORDER BY l.id DESC
                LIMIT $loan_limit
            ");
            $stmt->execute([$id, $id]);
            $loans = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    } catch (PDOException $e) {
        error_log('view_staff_details: ' . $e->getMessage());
        $error = 'Could not load the staff member: database error.';
    }
}

$status_badges = ['active' => 'success', 'pending' => 'warning', 'inactive' => 'secondary'];

include $base_path . 'includes/header.php';
?>

<div class="content-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h1><i class="fas fa-user-tie text-primary me-2"></i><?php echo __('staff_details'); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item"><a href="view_staff.php"><?php echo __('staff'); ?></a></li>
                <li class="breadcrumb-item active"><?php echo __('staff_details'); ?></li>
            </ol>
        </nav>
    </div>
    <?php if ($member): ?>
    <div class="d-flex gap-2">
        <a href="edit_staff.php?id=<?php echo (int)$member['id']; ?>" class="btn btn-primary"><i class="fas fa-edit me-2"></i>Edit</a>
        <a href="view_staff.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Back</a>
    </div>
    <?php endif; ?>
</div>

<?php if ($error): ?>
<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?></div>
<?php elseif (!$member): ?>
<div class="alert alert-warning">
    <i class="fas fa-exclamation-triangle me-2"></i>Staff member not found.
    <a href="view_staff.php" class="alert-link">Back to staff list</a>.
</div>
<?php else: ?>
<?php $badge = $status_badges[$member['status']] ?? 'secondary'; ?>
<div class="row">
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-body text-center">
                <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px; font-size: 1.75rem;">
                    <?php echo htmlspecialchars(mb_strtoupper(mb_substr($member['fname'] ?? '', 0, 1) . mb_substr($member['lname'] ?? '', 0, 1))); ?>
                </div>
                <h4 class="mb-1"><?php echo htmlspecialchars(trim(($member['fname'] ?? '') . ' ' . ($member['lname'] ?? ''))); ?></h4>
                <p class="text-muted mb-2"><?php echo htmlspecialchars($member['role_name'] ?? 'No role'); ?></p>
                <span class="badge bg-<?php echo $badge; ?>"><?php echo htmlspecialchars(ucfirst($member['status'])); ?></span>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between">
                    <span class="text-muted"><i class="fas fa-envelope me-2"></i>Email</span>
                    <span><?php echo htmlspecialchars($member['email'] ?: '-'); ?></span>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span class="text-muted"><i class="fas fa-phone me-2"></i>Phone</span>
                    <span><?php echo htmlspecialchars($member['phone_no'] ?: '-'); ?></span>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span class="text-muted"><i class="fas fa-building me-2"></i>Branch</span>
                    <span><?php echo htmlspecialchars($member['branch_name'] ?? '-'); ?></span>
                </li>
                <?php if (!empty($member['gender'])): ?>
                <li class="list-group-item d-flex justify-content-between">
                    <span class="text-muted"><i class="fas fa-venus-mars me-2"></i>Gender</span>
                    <span><?php echo htmlspecialchars($member['gender']); ?></span>
                </li>
                <?php endif; ?>
                <?php if (!empty($member['address']) || !empty($member['city'])): ?>
                <li class="list-group-item d-flex justify-content-between">
                    <span class="text-muted"><i class="fas fa-map-marker-alt me-2"></i>Address</span>
                    <span class="text-end"><?php echo htmlspecialchars(trim(($member['address'] ?? '') . ' ' . ($member['city'] ?? ''))); ?></span>
                </li>
                <?php endif; ?>
                <li class="list-group-item d-flex justify-content-between">
                    <span class="text-muted"><i class="fas fa-calendar me-2"></i>Joined</span>
                    <span><?php echo htmlspecialchars(date('d/m/Y', strtotime($member['created_at']))); ?></span>
                </li>
            </ul>
        </div>
    </div>

    <div class="col-lg-8 mb-4">
        <div class="row g-3 mb-3">
            <div class="col-sm-6">
                <div class="card stat-card">
                    <div class="stat-icon primary"><i class="fas fa-file-invoice-dollar"></i></div>
                    <div class="stat-info">
                        <h3><?php echo number_format($loan_total); ?></h3>
                        <p>Assigned Loans</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="card stat-card">
                    <div class="stat-icon success"><i class="fas fa-coins"></i></div>
                    <div class="stat-info">
                        <h3>TZS <?php echo number_format($portfolio_total, 2); ?></h3>
                        <p>Total Principal</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-list me-2"></i>Loans Assigned (Loan Officer / Collector)</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Loan #</th>
                                <th>Borrower</th>
                                <th>Principal</th>
                                <th>Released</th>
                                <th>Status</th>
                                <th>Assigned As</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($loans)): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-2x mb-2 d-block"></i>No loans assigned to this staff member.
                                </td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($loans as $loan): ?>
                            <?php
                            $roles = [];
                            if ((int)$loan['loan_officer_id'] === $id) { $roles[] = 'Loan Officer'; }
                            if ((int)$loan['collector_id'] === $id) { $roles[] = 'Collector'; }
                            ?>
                            <tr>
                                <td><a href="<?php echo $base_path; ?>loans/view_loan_details.php?id=<?php echo (int)$loan['id']; ?>" class="fw-semibold"><?php echo htmlspecialchars($loan['loan_no'] ?: '#' . $loan['id']); ?></a></td>
                                <td><?php echo htmlspecialchars(trim(($loan['fname'] ?? '') . ' ' . ($loan['lname'] ?? '')) ?: '-'); ?></td>
                                <td>TZS <?php echo number_format((float)$loan['principal_amount'], 2); ?></td>
                                <td><?php echo $loan['release_date'] ? htmlspecialchars(date('d/m/Y', strtotime($loan['release_date']))) : '-'; ?></td>
                                <td><?php echo htmlspecialchars($loan['status'] ?: '-'); ?></td>
                                <td><?php echo htmlspecialchars(implode(', ', $roles)); ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php if ($loan_total > count($loans)): ?>
            <div class="card-footer text-muted small">Showing the latest <?php echo count($loans); ?> of <?php echo number_format($loan_total); ?> loans.</div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<?php include $base_path . 'includes/footer.php'; ?>
