<?php
$base_path = '../';
$page_title = 'General Ledger';
$current_page = 'accounting';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$date_from = $_GET['from'] ?? date('Y-01-01');
$date_to = $_GET['to'] ?? date('Y-m-d');

$ledger_entries = [
    ['date' => '2026-01-02', 'ref' => 'JE-001', 'description' => 'Loan Disbursement - John Doe', 'account' => 'Loans Receivable', 'debit' => 5000000, 'credit' => 0],
    ['date' => '2026-01-02', 'ref' => 'JE-001', 'description' => 'Loan Disbursement - John Doe', 'account' => 'Cash & Bank', 'debit' => 0, 'credit' => 5000000],
    ['date' => '2026-01-05', 'ref' => 'JE-002', 'description' => 'Loan Repayment - Jane Smith', 'account' => 'Cash & Bank', 'debit' => 250000, 'credit' => 0],
    ['date' => '2026-01-05', 'ref' => 'JE-002', 'description' => 'Loan Repayment - Jane Smith', 'account' => 'Loans Receivable', 'debit' => 0, 'credit' => 200000],
    ['date' => '2026-01-05', 'ref' => 'JE-002', 'description' => 'Interest Income - Jane Smith', 'account' => 'Interest Income', 'debit' => 0, 'credit' => 50000],
    ['date' => '2026-01-10', 'ref' => 'JE-003', 'description' => 'Salary Payment', 'account' => 'Salaries & Wages', 'debit' => 1500000, 'credit' => 0],
    ['date' => '2026-01-10', 'ref' => 'JE-003', 'description' => 'Salary Payment', 'account' => 'Cash & Bank', 'debit' => 0, 'credit' => 1500000],
    ['date' => '2026-01-15', 'ref' => 'JE-004', 'description' => 'Office Rent', 'account' => 'Rent Expense', 'debit' => 500000, 'credit' => 0],
    ['date' => '2026-01-15', 'ref' => 'JE-004', 'description' => 'Office Rent', 'account' => 'Cash & Bank', 'debit' => 0, 'credit' => 500000],
];

include $base_path . 'includes/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><?php echo __('general_ledger'); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item"><a href="#"><?php echo __('accounting'); ?></a></li>
                <li class="breadcrumb-item active"><?php echo __('general_ledger'); ?></li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary" onclick="window.print()"><i class="fas fa-print me-2"></i><?php echo __('print'); ?></button>
        <button class="btn btn-outline-secondary"><i class="fas fa-download me-2"></i><?php echo __('export'); ?></button>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label"><?php echo __('period_from'); ?></label>
                <input type="date" name="from" class="form-control" value="<?php echo $date_from; ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label"><?php echo __('period_to'); ?></label>
                <input type="date" name="to" class="form-control" value="<?php echo $date_to; ?>">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary"><i class="fas fa-filter me-2"></i><?php echo __('generate_report'); ?></button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-book me-2"></i><?php echo __('general_ledger'); ?></h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th><?php echo __('date'); ?></th>
                        <th><?php echo __('reference'); ?></th>
                        <th><?php echo __('description'); ?></th>
                        <th><?php echo __('account_name'); ?></th>
                        <th class="text-end"><?php echo __('debit'); ?></th>
                        <th class="text-end"><?php echo __('credit'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ledger_entries as $entry): ?>
                    <tr>
                        <td><?php echo date('M d, Y', strtotime($entry['date'])); ?></td>
                        <td><span class="badge bg-secondary"><?php echo $entry['ref']; ?></span></td>
                        <td><?php echo htmlspecialchars($entry['description']); ?></td>
                        <td><?php echo htmlspecialchars($entry['account']); ?></td>
                        <td class="text-end"><?php echo $entry['debit'] > 0 ? 'TZS ' . number_format($entry['debit'], 2) : '-'; ?></td>
                        <td class="text-end"><?php echo $entry['credit'] > 0 ? 'TZS ' . number_format($entry['credit'], 2) : '-'; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include $base_path . 'includes/footer.php'; ?>
