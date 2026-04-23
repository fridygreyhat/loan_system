<?php
$base_path = '../';
$page_title = 'Loan Details';
$current_page = 'loans';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$loan_id = (int)($_GET['id'] ?? $_GET['loan_id'] ?? 0);

$loan = [];
$borrower = [];
$repayments = [];
$applied_charges = [];
$error = '';

if (!$loan_id) {
    header("Location: view_loans.php");
    exit;
}

try {
    $stmt = $con->prepare("
        SELECT l.*, b.fname, b.lname, b.mobile_no, b.email, b.address, b.city, b.country, b.photo, b.field2 as photo_path,
               lp.name as product_name, lp.decimal_places
        FROM loans l
        LEFT JOIN borrower b ON l.borrower_id = b.id
        LEFT JOIN loan_product lp ON l.loan_product_id = lp.id
        WHERE l.id = ?
    ");
    $stmt->execute([$loan_id]);
    $loan = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$loan) {
        $error = 'Loan not found.';
    } else {
        $stmt2 = $con->prepare("SELECT * FROM loan_repayment WHERE loan_id = ? ORDER BY paid_date DESC");
        $stmt2->execute([$loan_id]);
        $repayments = $stmt2->fetchAll(PDO::FETCH_ASSOC);

        $stmt3 = $con->prepare("
            SELECT lac.*, lc.name as charge_name, lc.calculation_type
            FROM loan_applied_charges lac
            LEFT JOIN loan_charges lc ON lac.loan_charge_id = lc.id
            WHERE lac.loan_id = ?
        ");
        $stmt3->execute([$loan_id]);
        $applied_charges = $stmt3->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    $error = $e->getMessage();
}

$total_paid = array_sum(array_column($repayments, 'amount'));
$outstanding = ($loan['principal_amt'] ?? 0) - $total_paid;
$pct_paid = ($loan['principal_amt'] ?? 0) > 0 ? round(($total_paid / $loan['principal_amt']) * 100, 1) : 0;

include $base_path . 'includes/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><?php echo __('loan_details'); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item"><a href="view_loans.php"><?php echo __('loans'); ?></a></li>
                <li class="breadcrumb-item active"><?php echo __('loan_details'); ?></li>
            </ol>
        </nav>
    </div>
    <?php if ($loan): ?>
    <div class="d-flex gap-2">
        <a href="../repayments/add_repayment.php?loan_id=<?php echo $loan_id; ?>" class="btn btn-success"><i class="fas fa-plus me-2"></i><?php echo __('record_payment'); ?></a>
        <div class="dropdown">
            <button class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown"><i class="fas fa-ellipsis-v"></i></button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="edit_loan.php?loan_id=<?php echo $loan_id; ?>"><i class="fas fa-edit me-2"></i><?php echo __('edit_loan'); ?></a></li>
                <li><a class="dropdown-item" href="loan_statement.php?loan_id=<?php echo $loan_id; ?>"><i class="fas fa-file-alt me-2"></i><?php echo __('loan_statement'); ?></a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="delete_loan.php?loan_id=<?php echo $loan_id; ?>" onclick="return confirm('<?php echo __('confirm_delete_loan'); ?>')"><i class="fas fa-trash me-2"></i><?php echo __('delete_loan'); ?></a></li>
            </ul>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php if ($error): ?>
<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?></div>
<?php elseif ($loan): ?>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="stat-icon primary"><i class="fas fa-money-bill-wave"></i></div>
            <div class="stat-info">
                <h3>TZS <?php echo number_format($loan['principal_amt'] ?? 0, 2); ?></h3>
                <p><?php echo __('principal_amount'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="stat-icon success"><i class="fas fa-check-circle"></i></div>
            <div class="stat-info">
                <h3>TZS <?php echo number_format($total_paid, 2); ?></h3>
                <p><?php echo __('total_paid'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="stat-icon danger"><i class="fas fa-exclamation-circle"></i></div>
            <div class="stat-info">
                <h3>TZS <?php echo number_format(max(0, $outstanding), 2); ?></h3>
                <p><?php echo __('outstanding'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="stat-icon warning"><i class="fas fa-percentage"></i></div>
            <div class="stat-info">
                <h3><?php echo $pct_paid; ?>%</h3>
                <p><?php echo __('repaid'); ?></p>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><h5><i class="fas fa-user me-2"></i><?php echo __('borrower_information'); ?></h5></div>
            <div class="card-body text-center pb-2">
                <?php
                $photoSrc = 'https://ui-avatars.com/api/?name=' . urlencode(($loan['fname'] ?? 'U') . '+' . ($loan['lname'] ?? 'N')) . '&size=100&background=2c7be5&color=fff';
                if (!empty($loan['photo_path']) && file_exists($base_path . $loan['photo_path'])) {
                    $photoSrc = $base_path . $loan['photo_path'];
                }
                ?>
                <img src="<?php echo $photoSrc; ?>" class="rounded-circle mb-3" style="width:80px;height:80px;object-fit:cover;">
                <h5 class="mb-0"><?php echo htmlspecialchars(($loan['fname'] ?? '') . ' ' . ($loan['lname'] ?? '')); ?></h5>
                <p class="text-muted small mb-3"><?php echo htmlspecialchars($loan['email'] ?? ''); ?></p>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between">
                    <span class="text-muted"><?php echo __('phone'); ?></span>
                    <span><?php echo htmlspecialchars($loan['mobile_no'] ?? '-'); ?></span>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span class="text-muted"><?php echo __('address'); ?></span>
                    <span><?php echo htmlspecialchars(trim(($loan['address'] ?? '') . ' ' . ($loan['city'] ?? ''))); ?></span>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span class="text-muted"><?php echo __('country'); ?></span>
                    <span><?php echo htmlspecialchars($loan['country'] ?? '-'); ?></span>
                </li>
            </ul>
            <div class="card-body pt-2">
                <a href="../borrowers/view_borrowers.php" class="btn btn-sm btn-outline-primary w-100"><?php echo __('view_profile'); ?></a>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" id="loanTabs">
                    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tab-terms"><?php echo __('loan_terms'); ?></a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-repayments"><?php echo __('repayments'); ?></a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-schedule"><?php echo __('schedule'); ?></a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-charges"><?php echo __('charges_fees'); ?></a></li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="tab-terms">
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="p-3 bg-light rounded">
                                    <small class="text-muted d-block"><?php echo __('loan_no'); ?></small>
                                    <strong><?php echo htmlspecialchars($loan['loan_no'] ?? 'L-' . $loan_id); ?></strong>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 bg-light rounded">
                                    <small class="text-muted d-block"><?php echo __('status'); ?></small>
                                    <?php
                                    $status = strtolower($loan['status'] ?? 'active');
                                    $badgeClass = match($status) {
                                        'active' => 'bg-success',
                                        'pending','pending_approval' => 'bg-warning text-dark',
                                        'paid','closed' => 'bg-secondary',
                                        'overdue','arrears' => 'bg-danger',
                                        default => 'bg-primary'
                                    };
                                    ?>
                                    <span class="badge <?php echo $badgeClass; ?> fs-6"><?php echo htmlspecialchars(ucfirst($loan['status'] ?? 'Active')); ?></span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 bg-light rounded">
                                    <small class="text-muted d-block"><?php echo __('loan_product'); ?></small>
                                    <strong><?php echo htmlspecialchars($loan['product_name'] ?? '-'); ?></strong>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 bg-light rounded">
                                    <small class="text-muted d-block"><?php echo __('principal_amount'); ?></small>
                                    <strong>TZS <?php echo number_format($loan['principal_amt'] ?? 0, 2); ?></strong>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 bg-light rounded">
                                    <small class="text-muted d-block"><?php echo __('release_date'); ?></small>
                                    <strong><?php echo htmlspecialchars($loan['release_date'] ?? '-'); ?></strong>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 bg-light rounded">
                                    <small class="text-muted d-block"><?php echo __('disbursed_by'); ?></small>
                                    <strong><?php echo htmlspecialchars($loan['disbursement_mtd'] ?? '-'); ?></strong>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 bg-light rounded">
                                    <small class="text-muted d-block"><?php echo __('interest_method'); ?></small>
                                    <strong><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $loan['interest_mtd'] ?? '-'))); ?></strong>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 bg-light rounded">
                                    <small class="text-muted d-block"><?php echo __('interest_rate'); ?></small>
                                    <strong><?php echo htmlspecialchars($loan['loan_interest'] ?? '0'); ?>% <?php echo htmlspecialchars($loan['loan_interest_pd'] ?? ''); ?></strong>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 bg-light rounded">
                                    <small class="text-muted d-block"><?php echo __('loan_duration'); ?></small>
                                    <strong><?php echo htmlspecialchars($loan['loan_duration'] ?? '-'); ?> <?php echo htmlspecialchars($loan['loan_duration_pd'] ?? ''); ?></strong>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 bg-light rounded">
                                    <small class="text-muted d-block"><?php echo __('repayment_cycle'); ?></small>
                                    <strong><?php echo htmlspecialchars(ucfirst($loan['repayment_cycle'] ?? '-')); ?></strong>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 bg-light rounded">
                                    <small class="text-muted d-block"><?php echo __('no_of_repayments'); ?></small>
                                    <strong><?php echo htmlspecialchars($loan['no_repayment_cycle'] ?? '-'); ?></strong>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="p-3 bg-light rounded">
                                    <small class="text-muted d-block mb-1"><?php echo __('repayment_progress'); ?></small>
                                    <div class="progress" style="height:10px;">
                                        <div class="progress-bar bg-success" style="width:<?php echo $pct_paid; ?>%"></div>
                                    </div>
                                    <small class="text-muted"><?php echo $pct_paid; ?>% <?php echo __('repaid'); ?> (TZS <?php echo number_format($total_paid, 2); ?> / TZS <?php echo number_format($loan['principal_amt'] ?? 0, 2); ?>)</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="tab-repayments">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0"><?php echo __('payment_history'); ?></h6>
                            <a href="../repayments/add_repayment.php?loan_id=<?php echo $loan_id; ?>" class="btn btn-sm btn-success"><i class="fas fa-plus me-1"></i><?php echo __('record_payment'); ?></a>
                        </div>
                        <?php if (empty($repayments)): ?>
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-receipt fa-2x mb-2"></i>
                            <p><?php echo __('no_repayments_made'); ?></p>
                        </div>
                        <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th><?php echo __('date'); ?></th>
                                        <th><?php echo __('amount'); ?></th>
                                        <th><?php echo __('principal'); ?></th>
                                        <th><?php echo __('interest'); ?></th>
                                        <th><?php echo __('receipt_no'); ?></th>
                                        <th><?php echo __('notes'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = 1; foreach ($repayments as $r): ?>
                                    <tr>
                                        <td><?php echo $i++; ?></td>
                                        <td><?php echo date('d M Y', strtotime($r['paid_date'])); ?></td>
                                        <td class="fw-semibold text-success">TZS <?php echo number_format($r['amount'], 2); ?></td>
                                        <td><?php echo number_format($r['principal'] ?? 0, 2); ?></td>
                                        <td><?php echo number_format($r['interest'] ?? 0, 2); ?></td>
                                        <td><?php echo htmlspecialchars($r['receipt_no'] ?? '-'); ?></td>
                                        <td><?php echo htmlspecialchars($r['notes'] ?? ''); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th colspan="2"><?php echo __('total'); ?></th>
                                        <th class="text-success">TZS <?php echo number_format($total_paid, 2); ?></th>
                                        <th colspan="4"></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="tab-pane fade" id="tab-schedule">
                        <?php
                        $principal = $loan['principal_amt'] ?? 0;
                        $numRepayments = $loan['no_repayment_cycle'] ?? 1;
                        $interestRate = $loan['loan_interest'] ?? 0;
                        $releaseDate = $loan['release_date'] ?? date('Y-m-d');
                        $cycle = strtolower($loan['repayment_cycle'] ?? 'monthly');
                        try {
                            $startDate = new DateTime($releaseDate);
                        } catch (Exception $e) {
                            $startDate = new DateTime();
                        }
                        $perPayment = $numRepayments > 0 ? $principal / $numRepayments : 0;
                        $intervalMap = ['daily' => 'P1D', 'weekly' => 'P7D', 'bi_weekly' => 'P14D', 'monthly' => 'P1M', 'quarterly' => 'P3M', 'yearly' => 'P1Y'];
                        $interval = $intervalMap[$cycle] ?? 'P1M';
                        ?>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th><?php echo __('due_date'); ?></th>
                                        <th><?php echo __('principal'); ?></th>
                                        <th><?php echo __('interest'); ?></th>
                                        <th><?php echo __('total_due'); ?></th>
                                        <th><?php echo __('status'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $currentDate = clone $startDate;
                                    $today = new DateTime();
                                    $repaymentAmounts = array_column($repayments, 'amount');
                                    $repaidIdx = 0;
                                    for ($n = 1; $n <= min($numRepayments, 60); $n++):
                                        $currentDate->add(new DateInterval($interval));
                                        $interestAmt = $perPayment * ($interestRate / 100);
                                        $totalDue = $perPayment + $interestAmt;
                                        $isPaid = isset($repaymentAmounts[$repaidIdx]) && $repaidIdx < count($repayments);
                                        $isOverdue = $currentDate < $today && !$isPaid;
                                    ?>
                                    <tr class="<?php echo $isOverdue ? 'table-danger' : ($isPaid ? 'table-success' : ''); ?>">
                                        <td><?php echo $n; ?></td>
                                        <td><?php echo $currentDate->format('d M Y'); ?></td>
                                        <td>TZS <?php echo number_format($perPayment, 2); ?></td>
                                        <td>TZS <?php echo number_format($interestAmt, 2); ?></td>
                                        <td class="fw-semibold">TZS <?php echo number_format($totalDue, 2); ?></td>
                                        <td>
                                            <?php if ($isPaid): $repaidIdx++; ?>
                                            <span class="badge bg-success"><?php echo __('paid'); ?></span>
                                            <?php elseif ($isOverdue): ?>
                                            <span class="badge bg-danger"><?php echo __('overdue'); ?></span>
                                            <?php else: ?>
                                            <span class="badge bg-light text-dark border"><?php echo __('pending'); ?></span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endfor; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="tab-charges">
                        <?php if (empty($applied_charges)): ?>
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-info-circle fa-2x mb-2"></i>
                            <p><?php echo __('no_charges_applied'); ?></p>
                        </div>
                        <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th><?php echo __('charge'); ?></th>
                                        <th><?php echo __('amount'); ?></th>
                                        <th><?php echo __('type'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($applied_charges as $charge): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($charge['charge_name'] ?? '-'); ?></td>
                                        <td>TZS <?php echo number_format($charge['amount'] ?? 0, 2); ?></td>
                                        <td><?php echo htmlspecialchars($charge['calculation_type'] ?? '-'); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php else: ?>
<div class="alert alert-warning"><i class="fas fa-exclamation-triangle me-2"></i>Loan not found. <a href="view_loans.php">Back to loans</a></div>
<?php endif; ?>

<?php include $base_path . 'includes/footer.php'; ?>
