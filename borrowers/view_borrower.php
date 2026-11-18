<?php
$base_path = '../';
$page_title = 'Borrower Profile';
$current_page = 'borrowers';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$borrower_id = (int)($_GET['id'] ?? 0);

$borrower = null;
$loans = [];
$savings = [];
$error = '';

if ($borrower_id > 0) {
    try {
        $stmt = $con->prepare("
            SELECT b.*, g.group_name, br.name AS branch_name, s.fname AS staff_fname, s.lname AS staff_lname
            FROM borrower b
            LEFT JOIN borrower_groups g ON g.id = b.group_id
            LEFT JOIN branches br ON br.id = b.branch_id
            LEFT JOIN staff s ON s.id = b.staff_id
            WHERE b.id = ?
        ");
        $stmt->execute([$borrower_id]);
        $borrower = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;

        if ($borrower) {
            $stmt = $con->prepare("
                SELECT l.id, l.loan_no, l.principal_amount, l.release_date, l.maturity_date, l.status,
                       lp.name AS product_name,
                       COALESCE((SELECT SUM(r.amount) FROM loan_repayment r WHERE r.loan_id = l.id), 0) AS total_paid
                FROM loans l
                LEFT JOIN loan_product lp ON lp.id = l.loan_product_id
                WHERE l.borrower_id = ?
                ORDER BY l.release_date DESC NULLS LAST, l.id DESC
            ");
            $stmt->execute([$borrower_id]);
            $loans = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $stmt = $con->prepare("
                SELECT sa.*, sp.name AS product_name
                FROM savings_accounts sa
                LEFT JOIN savings_products sp ON sp.id = sa.product_id
                WHERE sa.borrower_id = ?
                ORDER BY sa.opened_at DESC
            ");
            $stmt->execute([$borrower_id]);
            $savings = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    } catch (PDOException $e) {
        $error = 'Could not load borrower: ' . $e->getMessage();
    }
}

$total_borrowed = array_sum(array_map('floatval', array_column($loans, 'principal_amount')));
$total_paid = array_sum(array_map('floatval', array_column($loans, 'total_paid')));
$total_savings = array_sum(array_map('floatval', array_column($savings, 'balance')));

function vb_status_badge(?string $status): string
{
    $s = strtolower((string)$status);
    $class = match ($s) {
        'approved', 'active' => 'bg-success',
        'pending', 'pending_approval' => 'bg-warning text-dark',
        'paid', 'closed' => 'bg-secondary',
        'denied', 'defaulted' => 'bg-danger',
        default => 'bg-primary',
    };
    return '<span class="badge ' . $class . '">' . htmlspecialchars(ucfirst($status ?? '-')) . '</span>';
}

$flash_error = $_SESSION['flash_error'] ?? '';
unset($_SESSION['flash_error']);

include $base_path . 'includes/header.php';
?>
<?php if (isset($_GET['success'])): ?>
<div class="alert alert-success"><i class="fas fa-check-circle me-2"></i>Borrower saved.</div>
<?php endif; ?>
<?php if ($flash_error): ?>
<div class="alert alert-warning"><i class="fas fa-exclamation-triangle me-2"></i><?php echo htmlspecialchars($flash_error); ?></div>
<?php endif; ?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><i class="fas fa-user text-primary me-2"></i>Borrower Profile</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item"><a href="view_borrowers.php"><?php echo __('borrowers'); ?></a></li>
                <li class="breadcrumb-item active"><?php echo $borrower ? htmlspecialchars($borrower['fname'] . ' ' . $borrower['lname']) : __('view'); ?></li>
            </ol>
        </nav>
    </div>
    <?php if ($borrower): ?>
    <div class="d-flex gap-2">
        <a href="../loans/add_loan.php?borrower_id=<?php echo $borrower_id; ?>" class="btn btn-primary"><i class="fas fa-plus me-2"></i><?php echo __('add_loan'); ?></a>
        <a href="add_borrower.php?id=<?php echo $borrower_id; ?>" class="btn btn-outline-secondary"><i class="fas fa-edit me-2"></i><?php echo __('edit'); ?></a>
    </div>
    <?php endif; ?>
</div>

<?php if ($error): ?>
<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?></div>
<?php elseif (!$borrower): ?>
<div class="alert alert-warning"><i class="fas fa-exclamation-triangle me-2"></i>Borrower not found. <a href="view_borrowers.php">Back to borrowers</a></div>
<?php else: ?>

<?php
$photoSrc = 'https://ui-avatars.com/api/?name=' . urlencode($borrower['fname'] . '+' . $borrower['lname']) . '&size=120&background=2c7be5&color=fff';
$photoPath = (string)($borrower['field2'] ?? '');
if ($photoPath !== '' && !str_contains($photoPath, '..') && is_file($base_path . $photoPath)) {
    $photoSrc = $base_path . $photoPath;
}
$details = [
    __('title')          => $borrower['title'],
    __('gender')         => $borrower['gender'],
    'Date of Birth'      => $borrower['dob'] ? date('d M Y', strtotime($borrower['dob'])) : null,
    'Unique ID'          => $borrower['unique_no'],
    __('mobile')         => $borrower['mobile_no'],
    __('landline')       => $borrower['landline'],
    __('email')          => $borrower['email'],
    __('address')        => $borrower['address'],
    __('city')           => $borrower['city'],
    __('province_state') => $borrower['province_state'],
    'Zip Code'           => $borrower['zipcode'],
    __('country')        => $borrower['country'],
    'Business Name'      => $borrower['business_name'],
    'Working Status'     => $borrower['working_status'],
    'Group'              => $borrower['group_name'],
    'Branch'             => $borrower['branch_name'],
    'Assigned Staff'     => trim(($borrower['staff_fname'] ?? '') . ' ' . ($borrower['staff_lname'] ?? '')),
    'Registered'         => $borrower['creation_date'] ? date('d M Y', strtotime($borrower['creation_date'])) : null,
];
?>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-body text-center">
                <img src="<?php echo htmlspecialchars($photoSrc); ?>" alt="" class="rounded-circle mb-3" style="width:110px;height:110px;object-fit:cover;">
                <h5 class="mb-0"><?php echo htmlspecialchars($borrower['fname'] . ' ' . $borrower['lname']); ?></h5>
                <p class="text-muted small mb-0"><?php echo htmlspecialchars($borrower['mobile_no'] ?? ''); ?></p>
            </div>
            <ul class="list-group list-group-flush">
                <?php foreach ($details as $label => $value): if ($value === null || $value === '') continue; ?>
                <li class="list-group-item d-flex justify-content-between gap-3">
                    <span class="text-muted"><?php echo htmlspecialchars($label); ?></span>
                    <span class="text-end">
                        <?php if ($label === 'Group' && !empty($borrower['group_id'])): ?>
                        <a href="groups/view_group.php?id=<?php echo (int)$borrower['group_id']; ?>"><?php echo htmlspecialchars($value); ?></a>
                        <?php else: ?>
                        <?php echo htmlspecialchars($value); ?>
                        <?php endif; ?>
                    </span>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php if (!empty($borrower['description'])): ?>
            <div class="card-body border-top">
                <small class="text-muted d-block"><?php echo __('description'); ?></small>
                <?php echo nl2br(htmlspecialchars($borrower['description'])); ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card stat-card">
                    <div class="stat-icon primary"><i class="fas fa-hand-holding-usd"></i></div>
                    <div class="stat-info"><h3>TZS <?php echo number_format($total_borrowed, 2); ?></h3><p>Total Borrowed</p></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card">
                    <div class="stat-icon success"><i class="fas fa-check-circle"></i></div>
                    <div class="stat-info"><h3>TZS <?php echo number_format($total_paid, 2); ?></h3><p><?php echo __('total_paid'); ?></p></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card">
                    <div class="stat-icon warning"><i class="fas fa-piggy-bank"></i></div>
                    <div class="stat-info"><h3>TZS <?php echo number_format($total_savings, 2); ?></h3><p><?php echo __('savings'); ?></p></div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-file-invoice-dollar me-2"></i><?php echo __('loans'); ?> (<?php echo count($loans); ?>)</h5>
                <a href="../loans/add_loan.php?borrower_id=<?php echo $borrower_id; ?>" class="btn btn-sm btn-primary"><i class="fas fa-plus me-1"></i><?php echo __('add_loan'); ?></a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th><?php echo __('loan_number'); ?></th>
                                <th>Product</th>
                                <th class="text-end"><?php echo __('principal'); ?></th>
                                <th class="text-end"><?php echo __('total_paid'); ?></th>
                                <th><?php echo __('release_date'); ?></th>
                                <th><?php echo __('maturity_date'); ?></th>
                                <th><?php echo __('status'); ?></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($loans)): ?>
                            <tr><td colspan="8" class="text-center text-muted py-4">No loans for this borrower yet.</td></tr>
                            <?php endif; ?>
                            <?php foreach ($loans as $l): ?>
                            <tr>
                                <td><a href="../loans/view_loan_details.php?id=<?php echo (int)$l['id']; ?>"><?php echo htmlspecialchars($l['loan_no'] ?: ('L-' . $l['id'])); ?></a></td>
                                <td><?php echo htmlspecialchars($l['product_name'] ?? '-'); ?></td>
                                <td class="text-end">TZS <?php echo number_format((float)$l['principal_amount'], 2); ?></td>
                                <td class="text-end">TZS <?php echo number_format((float)$l['total_paid'], 2); ?></td>
                                <td><?php echo $l['release_date'] ? date('d M Y', strtotime($l['release_date'])) : '-'; ?></td>
                                <td><?php echo $l['maturity_date'] ? date('d M Y', strtotime($l['maturity_date'])) : '-'; ?></td>
                                <td><?php echo vb_status_badge($l['status']); ?></td>
                                <td><a href="../loans/view_loan_details.php?id=<?php echo (int)$l['id']; ?>" class="btn btn-sm btn-outline-primary" title="<?php echo __('view'); ?>"><i class="fas fa-eye"></i></a></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-piggy-bank me-2"></i>Savings Accounts (<?php echo count($savings); ?>)</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th><?php echo __('account_number'); ?></th>
                                <th>Product</th>
                                <th class="text-end"><?php echo __('balance'); ?></th>
                                <th class="text-end">Interest Earned</th>
                                <th><?php echo __('status'); ?></th>
                                <th>Opened</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($savings)): ?>
                            <tr><td colspan="7" class="text-center text-muted py-4">No savings accounts.</td></tr>
                            <?php endif; ?>
                            <?php foreach ($savings as $s): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($s['account_number']); ?></td>
                                <td><?php echo htmlspecialchars($s['product_name'] ?? '-'); ?></td>
                                <td class="text-end">TZS <?php echo number_format((float)$s['balance'], 2); ?></td>
                                <td class="text-end">TZS <?php echo number_format((float)$s['interest_earned'], 2); ?></td>
                                <td><?php echo htmlspecialchars(ucfirst($s['status'] ?? '-')); ?></td>
                                <td><?php echo $s['opened_at'] ? date('d M Y', strtotime($s['opened_at'])) : '-'; ?></td>
                                <td><a href="../savings/view_account.php?id=<?php echo (int)$s['id']; ?>" class="btn btn-sm btn-outline-primary" title="<?php echo __('view'); ?>"><i class="fas fa-eye"></i></a></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php include $base_path . 'includes/footer.php'; ?>
