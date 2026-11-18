<?php
$base_path = '../';
$page_title = 'View Payroll';
$current_page = 'payroll';
include '../includes/header.php';
require_once '../helpers/connect.php';

$db = $con;

$payrolls = [];
try {
    $stmt = $db->query("SELECT p.*, CONCAT(s.fname, ' ', COALESCE(s.lname, '')) AS staff_name FROM payroll p LEFT JOIN staff s ON p.staff_id = s.id ORDER BY p.pay_date DESC, p.id DESC LIMIT 50");
    $payrolls = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $payrolls = [];
}

$total_payroll = 0;
$this_month_payroll = 0;
$staff_count = 0;
$current_month = date('Y-m');

try {
    $stmt = $db->query("SELECT COUNT(*) as count FROM staff WHERE status = 'active'");
    $staff_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
    
    $stmt = $db->query("SELECT COALESCE(SUM(net_pay), 0) as total FROM payroll");
    $total_payroll = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    
    $stmt = $db->prepare("SELECT COALESCE(SUM(net_pay), 0) as total FROM payroll WHERE pay_period = ?");
    $stmt->execute([$current_month]);
    $this_month_payroll = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
} catch (PDOException $e) {
    // Use defaults
}
?>

<div class="content-header">
    <h1><i class="fas fa-money-check-alt"></i> <?php echo __('payroll'); ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
            <li class="breadcrumb-item active"><?php echo __('payroll'); ?></li>
        </ol>
    </nav>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon bg-primary">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-details">
                <h3><?php echo number_format($staff_count); ?></h3>
                <p><?php echo __('active_staff'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon bg-success">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div class="stat-details">
                <h3>TZS <?php echo number_format($this_month_payroll, 0); ?></h3>
                <p><?php echo __('this_month_payroll'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon bg-info">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-details">
                <h3>TZS <?php echo number_format($total_payroll, 0); ?></h3>
                <p><?php echo __('total_payroll'); ?></p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><?php echo __('payroll_records'); ?></h5>
        <div>
            <a href="<?php echo $base_path; ?>payroll/add_payroll.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> <?php echo __('add_payroll'); ?>
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th><?php echo __('staff_name'); ?></th>
                        <th><?php echo __('pay_period'); ?></th>
                        <th><?php echo __('basic_salary'); ?></th>
                        <th><?php echo __('allowances'); ?></th>
                        <th><?php echo __('deductions'); ?></th>
                        <th><?php echo __('net_pay'); ?></th>
                        <th><?php echo __('status'); ?></th>
                        <th><?php echo __('actions'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($payrolls)): ?>
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                            <?php echo __('no_payroll_records'); ?>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($payrolls as $index => $payroll): ?>
                    <tr>
                        <td><?php echo $index + 1; ?></td>
                        <td><?php echo htmlspecialchars($payroll['staff_name'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars(date('M Y', strtotime(($payroll['pay_period'] ?? date('Y-m')) . '-01'))); ?></td>
                        <td>TZS <?php echo number_format($payroll['basic_salary'] ?? 0, 0); ?></td>
                        <td>TZS <?php echo number_format($payroll['allowances'] ?? 0, 0); ?></td>
                        <td>TZS <?php echo number_format($payroll['deductions'] ?? 0, 0); ?></td>
                        <td><strong>TZS <?php echo number_format($payroll['net_pay'] ?? 0, 0); ?></strong></td>
                        <td>
                            <span class="badge bg-<?php echo ($payroll['status'] ?? 'pending') === 'paid' ? 'success' : 'warning'; ?>">
                                <?php echo ucfirst($payroll['status'] ?? 'pending'); ?>
                            </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="view_payslip.php?id=<?php echo $payroll['id'] ?? 0; ?>" class="btn btn-outline-primary" title="<?php echo __('view_payslip'); ?>">
                                    <i class="fas fa-file-invoice"></i>
                                </a>
                                <a href="edit_payroll.php?id=<?php echo $payroll['id'] ?? 0; ?>" class="btn btn-outline-secondary" title="<?php echo __('edit'); ?>">
                                    <i class="fas fa-edit"></i>
                                </a>
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

<?php include '../includes/footer.php'; ?>
