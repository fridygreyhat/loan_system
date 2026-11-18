<?php
$base_path = '../';
$page_title = 'Reports';
$current_page = 'reports';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

// Known report pages => [group, icon, short description]. Any other .php page
// found in this directory is listed under "Other".
$known = [
    'loans.php'                   => ['Loans', 'fa-hand-holding-usd', 'Loans issued with balances and status.'],
    'borrowers.php'               => ['Loans', 'fa-users', 'Borrowers with their loans and balances.'],
    'disbursement.php'            => ['Loans', 'fa-money-bill-wave', 'Loans released in a period.'],
    'outstanding.php'             => ['Loans', 'fa-balance-scale', 'Outstanding principal, interest and fees.'],
    'fully_paid_loans.php'        => ['Loans', 'fa-check-circle', 'Loans that have been fully repaid.'],
    'defaulted_loans.php'         => ['Loans', 'fa-times-circle', 'Loans marked as defaulted.'],
    'loan_products.php'           => ['Loans', 'fa-boxes', 'Performance by loan product.'],
    'loan_officer.php'            => ['Loans', 'fa-user-tie', 'Loans and performance by loan officer.'],

    'collections.php'             => ['Collections', 'fa-hand-holding', 'Repayments collected in a period.'],
    'collector.php'               => ['Collections', 'fa-user-check', 'Collections by staff collector.'],
    'pro_rata_collections.php'    => ['Collections', 'fa-chart-pie', 'Monthly collections split pro-rata.'],
    'daily.php'                   => ['Collections', 'fa-calendar-day', 'Daily activity summary.'],
    'monthly.php'                 => ['Collections', 'fa-calendar-alt', 'Monthly activity summary.'],

    'profit_loss.php'             => ['Financial', 'fa-file-invoice-dollar', 'Income, expenses and net profit.'],
    'cash_flow.php'               => ['Financial', 'fa-exchange-alt', 'Cash in and cash out.'],
    'cash_flow_projections.php'   => ['Financial', 'fa-chart-line', 'Projected cash flow from schedules.'],
    'fees.php'                    => ['Financial', 'fa-receipt', 'Fees charged and collected.'],
    'deferred_income.php'         => ['Financial', 'fa-hourglass-half', 'Interest and fees not yet earned.'],
    'deferred_income_monthly.php' => ['Financial', 'fa-calendar-check', 'Deferred income by month.'],
    'all_entries.php'             => ['Financial', 'fa-book', 'All journal entries.'],

    'portfolio_risk.php'          => ['Portfolio', 'fa-exclamation-triangle', 'Portfolio at risk (PAR) by days overdue.'],
    'arrears_aging.php'           => ['Portfolio', 'fa-hourglass-end', 'Loan arrears grouped by age.'],
    'mfrs_ratios.php'             => ['Portfolio', 'fa-percentage', 'Microfinance performance ratios.'],
    'at_a_glance.php'             => ['Portfolio', 'fa-tachometer-alt', 'Key portfolio figures at a glance.'],
];

$group_order = [
    'Loans'       => 'fa-hand-holding-usd',
    'Collections' => 'fa-coins',
    'Financial'   => 'fa-calculator',
    'Portfolio'   => 'fa-chart-pie',
    'Other'       => 'fa-folder-open',
];
$groups = array_fill_keys(array_keys($group_order), []);

foreach (glob(__DIR__ . '/*.php') ?: [] as $path) {
    $file = basename($path);
    if ($file === 'index.php') {
        continue;
    }

    // Read the page's $page_title without executing it.
    $title = '';
    $head = (string)@file_get_contents($path, false, null, 0, 4096);
    if (preg_match('/\$page_title\s*=\s*([\'"])(.*?)\1\s*;/', $head, $m)) {
        $title = stripslashes($m[2]);
    }
    if ($title === '') {
        $title = ucwords(str_replace('_', ' ', basename($file, '.php'))) . ' Report';
    }

    [$group, $icon, $desc] = $known[$file] ?? ['Other', 'fa-file-alt', ''];
    $groups[$group][] = ['file' => $file, 'title' => $title, 'icon' => $icon, 'desc' => $desc];
}

foreach ($groups as &$items) {
    usort($items, fn($a, $b) => strcasecmp($a['title'], $b['title']));
}
unset($items);

include $base_path . 'includes/header.php';
?>

<div class="content-header">
    <h1><i class="fas fa-chart-bar text-primary me-2"></i><?php echo __('reports'); ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
            <li class="breadcrumb-item active"><?php echo __('reports'); ?></li>
        </ol>
    </nav>
</div>

<?php foreach ($groups as $group => $items): ?>
<?php if (empty($items)) continue; ?>
<div class="mb-4">
    <h5 class="mb-3"><i class="fas <?php echo $group_order[$group]; ?> text-primary me-2"></i><?php echo htmlspecialchars($group); ?></h5>
    <div class="row g-3">
        <?php foreach ($items as $r): ?>
        <div class="col-sm-6 col-lg-4 col-xl-3">
            <a href="<?php echo htmlspecialchars($r['file']); ?>" class="card h-100 text-decoration-none text-reset">
                <div class="card-body d-flex align-items-start">
                    <div class="me-3 text-primary"><i class="fas <?php echo htmlspecialchars($r['icon']); ?> fa-lg"></i></div>
                    <div>
                        <h6 class="mb-1"><?php echo htmlspecialchars($r['title']); ?></h6>
                        <?php if ($r['desc'] !== ''): ?>
                        <p class="small text-muted mb-0"><?php echo htmlspecialchars($r['desc']); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </a>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endforeach; ?>

<?php include $base_path . 'includes/footer.php'; ?>
