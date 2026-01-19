<?php
$base_path = '../';
$page_title = 'All Journal Entries';
$current_page = 'reports';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$entries = [];
$start_date = $_GET['start'] ?? date('Y-m-01');
$end_date = $_GET['end'] ?? date('Y-m-d');

try {
    $stmt = $con->prepare("
        SELECT je.*, coa.account_name, coa.account_code
        FROM journal_entries je
        LEFT JOIN chart_of_accounts coa ON je.account_id = coa.id
        WHERE je.entry_date BETWEEN ? AND ?
        ORDER BY je.entry_date DESC, je.id DESC
    ");
    $stmt->execute([$start_date, $end_date]);
    $entries = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
}

$total_debits = array_sum(array_column($entries, 'debit'));
$total_credits = array_sum(array_column($entries, 'credit'));

include $base_path . 'includes/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><i class="fas fa-book me-2"></i><?php echo __('all_entries'); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item"><a href="index.php"><?php echo __('reports'); ?></a></li>
                <li class="breadcrumb-item active"><?php echo __('all_entries'); ?></li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary"><i class="fas fa-print me-2"></i><?php echo __('print'); ?></button>
        <button class="btn btn-outline-secondary"><i class="fas fa-download me-2"></i><?php echo __('export'); ?></button>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label"><?php echo __('from_date'); ?></label>
                <input type="date" name="start" class="form-control" value="<?php echo $start_date; ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label"><?php echo __('to_date'); ?></label>
                <input type="date" name="end" class="form-control" value="<?php echo $end_date; ?>">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary"><i class="fas fa-filter me-2"></i><?php echo __('filter'); ?></button>
            </div>
        </form>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="stat-icon primary">
                <i class="fas fa-arrow-right"></i>
            </div>
            <div class="stat-info">
                <h3>TZS <?php echo number_format($total_debits, 2); ?></h3>
                <p><?php echo __('total_debits'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="stat-icon success">
                <i class="fas fa-arrow-left"></i>
            </div>
            <div class="stat-info">
                <h3>TZS <?php echo number_format($total_credits, 2); ?></h3>
                <p><?php echo __('total_credits'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="stat-icon info">
                <i class="fas fa-book"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo count($entries); ?></h3>
                <p><?php echo __('total_entries'); ?></p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-list me-2"></i><?php echo __('journal_entries'); ?></h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th><?php echo __('date'); ?></th>
                        <th><?php echo __('account'); ?></th>
                        <th><?php echo __('description'); ?></th>
                        <th><?php echo __('debit'); ?></th>
                        <th><?php echo __('credit'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($entries)): ?>
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">
                            <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                            <?php echo __('no_entries_found'); ?>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($entries as $entry): ?>
                    <tr>
                        <td><?php echo date('M d, Y', strtotime($entry['entry_date'])); ?></td>
                        <td>
                            <strong><?php echo htmlspecialchars($entry['account_code'] ?? ''); ?></strong>
                            <?php echo htmlspecialchars($entry['account_name'] ?? '-'); ?>
                        </td>
                        <td><?php echo htmlspecialchars($entry['description'] ?? '-'); ?></td>
                        <td class="text-primary"><?php echo ($entry['debit'] ?? 0) > 0 ? 'TZS ' . number_format($entry['debit'], 2) : '-'; ?></td>
                        <td class="text-success"><?php echo ($entry['credit'] ?? 0) > 0 ? 'TZS ' . number_format($entry['credit'], 2) : '-'; ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include $base_path . 'includes/footer.php'; ?>
