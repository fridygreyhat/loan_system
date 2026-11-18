<?php
$base_path = '../';
$page_title = 'Payslip';
$current_page = 'payroll';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$id = (int)($_GET['id'] ?? 0);
$error = '';
$payslip = null;

try {
    $stmt = $con->prepare("
        SELECT p.*, s.fname, s.lname, s.email AS staff_email, s.phone_no AS staff_phone
        FROM payroll p
        LEFT JOIN staff s ON s.id = p.staff_id
        WHERE p.id = ?
    ");
    $stmt->execute([$id]);
    $payslip = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
} catch (PDOException $e) {
    $error = 'Could not load payslip: ' . $e->getMessage();
}

function payslip_money($value): string
{
    return 'TZS ' . number_format((float)$value, 2);
}

function payslip_method_label(?string $method): string
{
    $labels = ['bank_transfer' => 'Bank Transfer', 'cash' => 'Cash', 'mobile_money' => 'Mobile Money'];
    $method = (string)$method;
    return $labels[$method] ?? ucwords(str_replace('_', ' ', $method));
}

function payslip_period_label(string $period): string
{
    $d = DateTime::createFromFormat('!Y-m', $period);
    return $d ? $d->format('F Y') : $period;
}

include $base_path . 'includes/header.php';
?>

<style>
@media print {
    .sidebar, .top-navbar, .no-print, .page-header { display: none !important; }
    .main-content { margin-left: 0 !important; padding: 0 !important; }
    .content-area { padding: 0 !important; }
    .card { border: none !important; box-shadow: none !important; }
}
</style>

<div class="page-header no-print">
    <div>
        <h1><i class="fas fa-file-invoice text-primary me-2"></i><?php echo __('view_payslip'); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item"><a href="view_payroll.php"><?php echo __('payroll'); ?></a></li>
                <li class="breadcrumb-item active"><?php echo __('view_payslip'); ?></li>
            </ol>
        </nav>
    </div>
</div>

<?php if ($error): ?>
<div class="alert alert-danger">
    <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?>
</div>
<?php endif; ?>

<?php if (!$payslip): ?>
<?php if (!$error): ?>
<div class="alert alert-warning">
    <i class="fas fa-exclamation-triangle me-2"></i>Payroll record not found.
    <a href="view_payroll.php" class="alert-link">Back to payroll</a>
</div>
<?php endif; ?>
<?php else:
    $staff_name = trim(($payslip['fname'] ?? '') . ' ' . ($payslip['lname'] ?? ''));
    if ($staff_name === '') {
        $staff_name = 'Staff #' . (int)$payslip['staff_id'];
    }
    $gross = (float)$payslip['basic_salary'] + (float)$payslip['allowances'] + (float)$payslip['overtime'] + (float)$payslip['bonus'];
    $total_deductions = (float)$payslip['tax'] + (float)$payslip['social_security'] + (float)$payslip['deductions'];
    $is_paid = $payslip['status'] === 'paid';
?>
<div class="d-flex gap-2 mb-3 no-print">
    <button type="button" class="btn btn-primary" onclick="window.print()">
        <i class="fas fa-print me-2"></i><?php echo __('print'); ?>
    </button>
    <a href="edit_payroll.php?id=<?php echo (int)$payslip['id']; ?>" class="btn btn-outline-secondary">
        <i class="fas fa-edit me-2"></i><?php echo __('edit'); ?>
    </a>
    <a href="view_payroll.php" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i><?php echo __('back'); ?>
    </a>
</div>

<div class="card">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-start mb-4">
            <div class="d-flex align-items-center gap-3">
                <img src="<?php echo $base_path; ?>uploads/logo.jpg" alt="Loan Store" style="height: 48px; width: auto;">
                <div>
                    <h4 class="mb-0">Loan Store</h4>
                    <small class="text-muted">Payslip</small>
                </div>
            </div>
            <div class="text-end">
                <div><strong>Payslip #<?php echo (int)$payslip['id']; ?></strong></div>
                <div><?php echo __('pay_period'); ?>: <?php echo htmlspecialchars(payslip_period_label((string)$payslip['pay_period'])); ?></div>
                <div>Pay Date: <?php echo htmlspecialchars(date('d M Y', strtotime((string)$payslip['pay_date']))); ?></div>
                <span class="badge bg-<?php echo $is_paid ? 'success' : 'warning'; ?>"><?php echo $is_paid ? __('paid') : __('pending'); ?></span>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-sm-6">
                <h6 class="text-uppercase text-muted mb-2"><?php echo __('staff_member'); ?></h6>
                <div class="fw-bold"><?php echo htmlspecialchars($staff_name); ?></div>
                <?php if (!empty($payslip['staff_email'])): ?><div><?php echo htmlspecialchars($payslip['staff_email']); ?></div><?php endif; ?>
                <?php if (!empty($payslip['staff_phone'])): ?><div><?php echo htmlspecialchars($payslip['staff_phone']); ?></div><?php endif; ?>
            </div>
            <div class="col-sm-6 text-sm-end">
                <h6 class="text-uppercase text-muted mb-2"><?php echo __('payment_method'); ?></h6>
                <div><?php echo htmlspecialchars(payslip_method_label($payslip['payment_method'])); ?></div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-6">
                <table class="table table-sm">
                    <thead class="table-light">
                        <tr><th colspan="2"><?php echo __('earnings'); ?></th></tr>
                    </thead>
                    <tbody>
                        <tr><td><?php echo __('basic_salary'); ?></td><td class="text-end"><?php echo payslip_money($payslip['basic_salary']); ?></td></tr>
                        <tr><td><?php echo __('allowances'); ?></td><td class="text-end"><?php echo payslip_money($payslip['allowances']); ?></td></tr>
                        <tr><td>Overtime</td><td class="text-end"><?php echo payslip_money($payslip['overtime']); ?></td></tr>
                        <tr><td>Bonus</td><td class="text-end"><?php echo payslip_money($payslip['bonus']); ?></td></tr>
                    </tbody>
                    <tfoot>
                        <tr class="fw-bold"><td><?php echo __('gross_earnings'); ?></td><td class="text-end"><?php echo payslip_money($gross); ?></td></tr>
                    </tfoot>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-sm">
                    <thead class="table-light">
                        <tr><th colspan="2"><?php echo __('deductions'); ?></th></tr>
                    </thead>
                    <tbody>
                        <tr><td><?php echo __('tax'); ?> (PAYE)</td><td class="text-end"><?php echo payslip_money($payslip['tax']); ?></td></tr>
                        <tr><td><?php echo __('nssf'); ?></td><td class="text-end"><?php echo payslip_money($payslip['social_security']); ?></td></tr>
                        <tr><td>Loan &amp; Other Deductions</td><td class="text-end"><?php echo payslip_money($payslip['deductions']); ?></td></tr>
                        <tr><td>&nbsp;</td><td></td></tr>
                    </tbody>
                    <tfoot>
                        <tr class="fw-bold"><td><?php echo __('total_deductions'); ?></td><td class="text-end text-danger"><?php echo payslip_money($total_deductions); ?></td></tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center border-top pt-3 mt-2">
            <span class="h5 mb-0"><?php echo __('net_pay'); ?></span>
            <span class="h4 mb-0 text-success"><?php echo payslip_money($payslip['net_pay']); ?></span>
        </div>

        <?php if (!empty($payslip['notes'])): ?>
        <div class="mt-4">
            <h6 class="text-uppercase text-muted mb-1"><?php echo __('notes'); ?></h6>
            <p class="mb-0"><?php echo nl2br(htmlspecialchars($payslip['notes'])); ?></p>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<?php include $base_path . 'includes/footer.php'; ?>
