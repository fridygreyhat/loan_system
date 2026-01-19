<?php
$base_path = '../';
$page_title = 'Chart of Accounts';
$current_page = 'accounting';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$accounts = [
    ['code' => '1000', 'name' => 'Cash & Bank', 'name_sw' => 'Fedha na Benki', 'type' => 'Asset', 'type_sw' => 'Mali', 'parent' => '-', 'status' => 'active'],
    ['code' => '1100', 'name' => 'Loans Receivable', 'name_sw' => 'Mikopo Inayopokelewa', 'type' => 'Asset', 'type_sw' => 'Mali', 'parent' => '-', 'status' => 'active'],
    ['code' => '1200', 'name' => 'Accounts Receivable', 'name_sw' => 'Akaunti Zinazopokelewa', 'type' => 'Asset', 'type_sw' => 'Mali', 'parent' => '-', 'status' => 'active'],
    ['code' => '1300', 'name' => 'Prepaid Expenses', 'name_sw' => 'Gharama Zilizolipwa Mapema', 'type' => 'Asset', 'type_sw' => 'Mali', 'parent' => '-', 'status' => 'active'],
    ['code' => '1500', 'name' => 'Property & Equipment', 'name_sw' => 'Mali na Vifaa', 'type' => 'Asset', 'type_sw' => 'Mali', 'parent' => '-', 'status' => 'active'],
    ['code' => '1510', 'name' => 'Accumulated Depreciation', 'name_sw' => 'Kushuka Thamani Jumla', 'type' => 'Asset', 'type_sw' => 'Mali', 'parent' => '1500', 'status' => 'active'],
    ['code' => '2000', 'name' => 'Accounts Payable', 'name_sw' => 'Akaunti Zinazolipwa', 'type' => 'Liability', 'type_sw' => 'Deni', 'parent' => '-', 'status' => 'active'],
    ['code' => '2100', 'name' => 'Accrued Expenses', 'name_sw' => 'Gharama Zilizokusanywa', 'type' => 'Liability', 'type_sw' => 'Deni', 'parent' => '-', 'status' => 'active'],
    ['code' => '2500', 'name' => 'Long Term Debt', 'name_sw' => 'Deni la Muda Mrefu', 'type' => 'Liability', 'type_sw' => 'Deni', 'parent' => '-', 'status' => 'active'],
    ['code' => '3000', 'name' => 'Capital', 'name_sw' => 'Mtaji', 'type' => 'Equity', 'type_sw' => 'Mtaji', 'parent' => '-', 'status' => 'active'],
    ['code' => '3100', 'name' => 'Retained Earnings', 'name_sw' => 'Faida Iliyohifadhiwa', 'type' => 'Equity', 'type_sw' => 'Mtaji', 'parent' => '-', 'status' => 'active'],
    ['code' => '4000', 'name' => 'Interest Income', 'name_sw' => 'Mapato ya Riba', 'type' => 'Revenue', 'type_sw' => 'Mapato', 'parent' => '-', 'status' => 'active'],
    ['code' => '4100', 'name' => 'Fee Income', 'name_sw' => 'Mapato ya Ada', 'type' => 'Revenue', 'type_sw' => 'Mapato', 'parent' => '-', 'status' => 'active'],
    ['code' => '4200', 'name' => 'Other Income', 'name_sw' => 'Mapato Mengine', 'type' => 'Revenue', 'type_sw' => 'Mapato', 'parent' => '-', 'status' => 'active'],
    ['code' => '5000', 'name' => 'Salaries & Wages', 'name_sw' => 'Mishahara', 'type' => 'Expense', 'type_sw' => 'Gharama', 'parent' => '-', 'status' => 'active'],
    ['code' => '5100', 'name' => 'Rent Expense', 'name_sw' => 'Gharama ya Kodi', 'type' => 'Expense', 'type_sw' => 'Gharama', 'parent' => '-', 'status' => 'active'],
    ['code' => '5200', 'name' => 'Utilities', 'name_sw' => 'Huduma', 'type' => 'Expense', 'type_sw' => 'Gharama', 'parent' => '-', 'status' => 'active'],
    ['code' => '5300', 'name' => 'Depreciation Expense', 'name_sw' => 'Gharama ya Kushuka Thamani', 'type' => 'Expense', 'type_sw' => 'Gharama', 'parent' => '-', 'status' => 'active'],
    ['code' => '5400', 'name' => 'Bad Debt Expense', 'name_sw' => 'Gharama ya Deni Baya', 'type' => 'Expense', 'type_sw' => 'Gharama', 'parent' => '-', 'status' => 'active'],
    ['code' => '5500', 'name' => 'Other Expenses', 'name_sw' => 'Gharama Nyingine', 'type' => 'Expense', 'type_sw' => 'Gharama', 'parent' => '-', 'status' => 'active'],
];

$lang = get_current_lang();

include $base_path . 'includes/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><?php echo __('chart_of_accounts'); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item"><a href="#"><?php echo __('accounting'); ?></a></li>
                <li class="breadcrumb-item active"><?php echo __('chart_of_accounts'); ?></li>
            </ol>
        </nav>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAccountModal">
        <i class="fas fa-plus me-2"></i><?php echo __('add'); ?> <?php echo __('account_name'); ?>
    </button>
</div>

<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-list me-2"></i><?php echo __('chart_of_accounts'); ?></h5>
        <input type="text" class="form-control form-control-sm" placeholder="<?php echo __('search'); ?>..." style="width: 200px;">
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th><?php echo __('account_code'); ?></th>
                        <th><?php echo __('account_name'); ?></th>
                        <th><?php echo __('account_type'); ?></th>
                        <th><?php echo __('parent_account'); ?></th>
                        <th><?php echo __('status'); ?></th>
                        <th><?php echo __('actions'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($accounts as $account): ?>
                    <tr>
                        <td><strong><?php echo $account['code']; ?></strong></td>
                        <td><?php echo $lang === 'sw' ? $account['name_sw'] : $account['name']; ?></td>
                        <td>
                            <?php
                            $type = $lang === 'sw' ? $account['type_sw'] : $account['type'];
                            $badge_class = 'secondary';
                            if ($account['type'] === 'Asset') $badge_class = 'success';
                            elseif ($account['type'] === 'Liability') $badge_class = 'danger';
                            elseif ($account['type'] === 'Equity') $badge_class = 'primary';
                            elseif ($account['type'] === 'Revenue') $badge_class = 'info';
                            elseif ($account['type'] === 'Expense') $badge_class = 'warning';
                            ?>
                            <span class="badge bg-<?php echo $badge_class; ?>"><?php echo $type; ?></span>
                        </td>
                        <td><?php echo $account['parent']; ?></td>
                        <td><span class="badge bg-success"><?php echo __($account['status']); ?></span></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary" title="<?php echo __('edit'); ?>"><i class="fas fa-edit"></i></button>
                                <button class="btn btn-outline-info" title="<?php echo __('view'); ?>"><i class="fas fa-eye"></i></button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="addAccountModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo __('add'); ?> <?php echo __('account_name'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('account_code'); ?></label>
                        <input type="text" class="form-control" placeholder="e.g., 1000">
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('account_name'); ?></label>
                        <input type="text" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('account_type'); ?></label>
                        <select class="form-select">
                            <option value="Asset"><?php echo __('assets'); ?></option>
                            <option value="Liability"><?php echo __('liabilities'); ?></option>
                            <option value="Equity"><?php echo __('equity'); ?></option>
                            <option value="Revenue"><?php echo __('revenue'); ?></option>
                            <option value="Expense"><?php echo __('expenses'); ?></option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('parent_account'); ?></label>
                        <select class="form-select">
                            <option value="">- <?php echo __('parent_account'); ?> -</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo __('cancel'); ?></button>
                <button type="button" class="btn btn-primary"><?php echo __('save'); ?></button>
            </div>
        </div>
    </div>
</div>

<?php include $base_path . 'includes/footer.php'; ?>
