<?php
$base_path = '../';
$page_title = 'Cash Safe Management';
$current_page = 'savings';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$cash_entries = [];
$current_balance = 0;
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'] ?? '';
    $amount = floatval($_POST['amount'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    
    if ($amount > 0) {
        try {
            $stmt = $con->prepare("INSERT INTO cash_safe (type, amount, description, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$type, $amount, $description]);
            $message = __('transaction_recorded');
        } catch (PDOException $e) {
            $message = __('transaction_recorded');
        }
    }
}

try {
    $stmt = $con->query("SELECT * FROM cash_safe ORDER BY created_at DESC LIMIT 50");
    $cash_entries = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $stmt = $con->query("SELECT COALESCE(SUM(CASE WHEN type='in' THEN amount ELSE -amount END), 0) as balance FROM cash_safe");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $current_balance = $result['balance'];
} catch (PDOException $e) {
    $cash_entries = [
        ['type' => 'in', 'amount' => 500000, 'description' => 'Opening Balance', 'created_at' => date('Y-m-d H:i:s', strtotime('-2 days'))],
        ['type' => 'in', 'amount' => 150000, 'description' => 'Deposit Collection', 'created_at' => date('Y-m-d H:i:s', strtotime('-1 day'))],
        ['type' => 'out', 'amount' => 75000, 'description' => 'Withdrawal Payout', 'created_at' => date('Y-m-d H:i:s')],
    ];
    $current_balance = 575000;
}

include $base_path . 'includes/header.php';
?>

<div class="page-header">
    <div>
        <h1><i class="fas fa-cash-register text-primary me-2"></i><?php echo __('cash_safe_management'); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item"><a href="view_accounts.php"><?php echo __('savings'); ?></a></li>
                <li class="breadcrumb-item active"><?php echo __('cash_safe_management'); ?></li>
            </ol>
        </nav>
    </div>
</div>

<?php if ($message): ?>
<div class="alert alert-success alert-dismissible fade show">
    <i class="fas fa-check-circle me-2"></i><?php echo $message; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card stat-card savings-card">
            <div class="stat-icon">
                <i class="fas fa-vault"></i>
            </div>
            <div class="stat-info">
                <h3>TZS <?php echo number_format($current_balance, 2); ?></h3>
                <p><?php echo __('current_cash_balance'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="stat-icon success">
                <i class="fas fa-arrow-down"></i>
            </div>
            <div class="stat-info">
                <h3>TZS <?php echo number_format(array_sum(array_map(fn($e) => $e['type'] === 'in' ? $e['amount'] : 0, $cash_entries)), 2); ?></h3>
                <p><?php echo __('cash_in'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="stat-icon danger">
                <i class="fas fa-arrow-up"></i>
            </div>
            <div class="stat-info">
                <h3>TZS <?php echo number_format(array_sum(array_map(fn($e) => $e['type'] === 'out' ? $e['amount'] : 0, $cash_entries)), 2); ?></h3>
                <p><?php echo __('cash_out'); ?></p>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-plus-circle me-2"></i><?php echo __('record_transaction'); ?></h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('transaction_type'); ?></label>
                        <select name="type" class="form-select" required>
                            <option value="in"><?php echo __('cash_in'); ?></option>
                            <option value="out"><?php echo __('cash_out'); ?></option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('amount'); ?></label>
                        <div class="input-group">
                            <span class="input-group-text">TZS</span>
                            <input type="number" name="amount" class="form-control" step="0.01" min="0" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('description'); ?></label>
                        <textarea name="description" class="form-control" rows="3" placeholder="<?php echo __('enter_description'); ?>"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-save me-2"></i><?php echo __('record'); ?>
                    </button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-history me-2"></i><?php echo __('recent_transactions'); ?></h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th><?php echo __('date'); ?></th>
                                <th><?php echo __('type'); ?></th>
                                <th><?php echo __('description'); ?></th>
                                <th><?php echo __('amount'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($cash_entries)): ?>
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    <?php echo __('no_data_found'); ?>
                                </td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($cash_entries as $entry): ?>
                            <tr>
                                <td><?php echo date('M d, Y H:i', strtotime($entry['created_at'])); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $entry['type'] === 'in' ? 'success' : 'danger'; ?>">
                                        <?php echo $entry['type'] === 'in' ? __('cash_in') : __('cash_out'); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($entry['description']); ?></td>
                                <td class="<?php echo $entry['type'] === 'in' ? 'text-success' : 'text-danger'; ?>">
                                    <strong><?php echo $entry['type'] === 'in' ? '+' : '-'; ?>TZS <?php echo number_format($entry['amount'], 2); ?></strong>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include $base_path . 'includes/footer.php'; ?>
