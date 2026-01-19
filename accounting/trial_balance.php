<?php
$base_path = '../';
$page_title = 'Trial Balance';
$current_page = 'accounting';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$as_of_date = $_GET['date'] ?? date('Y-m-d');

$accounts = [
    ['code' => '1000', 'name' => 'Cash & Bank', 'name_sw' => 'Fedha na Benki', 'debit' => 14523000, 'credit' => 0],
    ['code' => '1100', 'name' => 'Loans Receivable', 'name_sw' => 'Mikopo Inayopokelewa', 'debit' => 85000000, 'credit' => 0],
    ['code' => '1200', 'name' => 'Accounts Receivable', 'name_sw' => 'Akaunti Zinazopokelewa', 'debit' => 2500000, 'credit' => 0],
    ['code' => '1300', 'name' => 'Prepaid Expenses', 'name_sw' => 'Gharama Zilizolipwa Mapema', 'debit' => 1200000, 'credit' => 0],
    ['code' => '1500', 'name' => 'Property & Equipment', 'name_sw' => 'Mali na Vifaa', 'debit' => 5000000, 'credit' => 0],
    ['code' => '1510', 'name' => 'Accumulated Depreciation', 'name_sw' => 'Kushuka Thamani Jumla', 'debit' => 0, 'credit' => 500000],
    ['code' => '2000', 'name' => 'Accounts Payable', 'name_sw' => 'Akaunti Zinazolipwa', 'debit' => 0, 'credit' => 3200000],
    ['code' => '2100', 'name' => 'Accrued Expenses', 'name_sw' => 'Gharama Zilizokusanywa', 'debit' => 0, 'credit' => 1800000],
    ['code' => '2500', 'name' => 'Long Term Debt', 'name_sw' => 'Deni la Muda Mrefu', 'debit' => 0, 'credit' => 25000000],
    ['code' => '3000', 'name' => 'Capital', 'name_sw' => 'Mtaji', 'debit' => 0, 'credit' => 50000000],
    ['code' => '3100', 'name' => 'Retained Earnings', 'name_sw' => 'Faida Iliyohifadhiwa', 'debit' => 0, 'credit' => 15000000],
    ['code' => '4000', 'name' => 'Interest Income', 'name_sw' => 'Mapato ya Riba', 'debit' => 0, 'credit' => 4523000],
    ['code' => '4100', 'name' => 'Fee Income', 'name_sw' => 'Mapato ya Ada', 'debit' => 0, 'credit' => 875000],
    ['code' => '5000', 'name' => 'Salaries & Wages', 'name_sw' => 'Mishahara', 'debit' => 2875000, 'credit' => 0],
    ['code' => '5100', 'name' => 'Rent Expense', 'name_sw' => 'Gharama ya Kodi', 'debit' => 500000, 'credit' => 0],
    ['code' => '5200', 'name' => 'Utilities', 'name_sw' => 'Huduma', 'debit' => 125000, 'credit' => 0],
    ['code' => '5300', 'name' => 'Depreciation', 'name_sw' => 'Kushuka kwa Thamani', 'debit' => 250000, 'credit' => 0],
    ['code' => '5400', 'name' => 'Bad Debt Expense', 'name_sw' => 'Gharama ya Deni Baya', 'debit' => 320000, 'credit' => 0],
    ['code' => '5500', 'name' => 'Other Expenses', 'name_sw' => 'Gharama Nyingine', 'debit' => 480000, 'credit' => 0],
];

$total_debit = array_sum(array_column($accounts, 'debit'));
$total_credit = array_sum(array_column($accounts, 'credit'));
$lang = get_current_lang();

include $base_path . 'includes/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><?php echo __('trial_balance'); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item"><a href="#"><?php echo __('accounting'); ?></a></li>
                <li class="breadcrumb-item active"><?php echo __('trial_balance'); ?></li>
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
            <div class="col-md-6">
                <label class="form-label"><?php echo __('as_of_date'); ?></label>
                <input type="date" name="date" class="form-control" value="<?php echo $as_of_date; ?>">
            </div>
            <div class="col-md-6">
                <button type="submit" class="btn btn-primary"><i class="fas fa-filter me-2"></i><?php echo __('generate_report'); ?></button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-balance-scale me-2"></i><?php echo __('trial_balance'); ?> - <?php echo date('F d, Y', strtotime($as_of_date)); ?></h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th><?php echo __('account_code'); ?></th>
                        <th><?php echo __('account_name'); ?></th>
                        <th class="text-end"><?php echo __('debit'); ?></th>
                        <th class="text-end"><?php echo __('credit'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($accounts as $account): ?>
                    <tr>
                        <td><strong><?php echo $account['code']; ?></strong></td>
                        <td><?php echo $lang === 'sw' ? $account['name_sw'] : $account['name']; ?></td>
                        <td class="text-end"><?php echo $account['debit'] > 0 ? 'TZS ' . number_format($account['debit'], 2) : '-'; ?></td>
                        <td class="text-end"><?php echo $account['credit'] > 0 ? 'TZS ' . number_format($account['credit'], 2) : '-'; ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <tr class="table-primary">
                        <td colspan="2"><strong><?php echo __('total'); ?></strong></td>
                        <td class="text-end"><strong>TZS <?php echo number_format($total_debit, 2); ?></strong></td>
                        <td class="text-end"><strong>TZS <?php echo number_format($total_credit, 2); ?></strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <?php if ($total_debit == $total_credit): ?>
    <div class="card-footer bg-success text-white text-center">
        <i class="fas fa-check-circle me-2"></i> <?php echo __('trial_balance'); ?> <?php echo __('completed'); ?>
    </div>
    <?php endif; ?>
</div>

<?php include $base_path . 'includes/footer.php'; ?>
