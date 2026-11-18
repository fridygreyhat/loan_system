<?php
$base_path = '../';
$page_title = 'Investor Profile';
$current_page = 'investors';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$id = (int)($_GET['id'] ?? 0);
$error = '';
$investor = null;
$accounts = [];
$transactions = [];

try {
    $stmt = $con->prepare("SELECT * FROM investors WHERE id = ?");
    $stmt->execute([$id]);
    $investor = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;

    if ($investor) {
        $stmt = $con->prepare("SELECT * FROM investor_accounts WHERE investor_id = ? ORDER BY created_at DESC, id DESC");
        $stmt->execute([$id]);
        $accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = $con->prepare("
            SELECT t.*, a.account_number
            FROM investor_transactions t
            JOIN investor_accounts a ON a.id = t.account_id
            WHERE a.investor_id = ?
            ORDER BY t.created_at DESC, t.id DESC
            LIMIT 25
        ");
        $stmt->execute([$id]);
        $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    $error = 'Could not load investor: ' . $e->getMessage();
}

function inv_money($value): string
{
    return 'TZS ' . number_format((float)$value, 2);
}

$total_invested = array_sum(array_map('floatval', array_column($accounts, 'investment_amount')));
$total_interest = array_sum(array_map('floatval', array_column($accounts, 'interest_earned')));

include $base_path . 'includes/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><i class="fas fa-user-tie text-primary me-2"></i><?php echo $investor ? htmlspecialchars($investor['first_name'] . ' ' . $investor['last_name']) : 'Investor'; ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item"><a href="view_investors.php"><?php echo __('investors'); ?></a></li>
                <li class="breadcrumb-item active"><?php echo __('investor_details'); ?></li>
            </ol>
        </nav>
    </div>
    <?php if ($investor): ?>
    <div class="d-flex gap-2">
        <a href="add_investment.php?id=<?php echo (int)$investor['id']; ?>" class="btn btn-success">
            <i class="fas fa-plus me-2"></i>Add Investment
        </a>
        <a href="edit_investor.php?id=<?php echo (int)$investor['id']; ?>" class="btn btn-outline-secondary">
            <i class="fas fa-edit me-2"></i><?php echo __('edit'); ?>
        </a>
    </div>
    <?php endif; ?>
</div>

<?php if ($error): ?>
<div class="alert alert-danger">
    <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?>
</div>
<?php elseif (!$investor): ?>
<div class="alert alert-warning">
    <i class="fas fa-exclamation-triangle me-2"></i>Investor not found.
    <a href="view_investors.php" class="alert-link">Back to investors</a>
</div>
<?php endif; ?>

<?php if ($investor): ?>
<div class="row g-4 mb-4">
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header">
                <h5><i class="fas fa-id-card me-2"></i><?php echo __('investor_details'); ?></h5>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-5"><?php echo __('first_name'); ?></dt>
                    <dd class="col-7"><?php echo htmlspecialchars($investor['first_name']); ?></dd>
                    <dt class="col-5"><?php echo __('last_name'); ?></dt>
                    <dd class="col-7"><?php echo htmlspecialchars($investor['last_name']); ?></dd>
                    <dt class="col-5"><?php echo __('email'); ?></dt>
                    <dd class="col-7"><?php echo htmlspecialchars($investor['email'] ?: '-'); ?></dd>
                    <dt class="col-5"><?php echo __('phone'); ?></dt>
                    <dd class="col-7"><?php echo htmlspecialchars($investor['phone'] ?: '-'); ?></dd>
                    <dt class="col-5"><?php echo __('id_number'); ?></dt>
                    <dd class="col-7"><?php echo htmlspecialchars($investor['id_number'] ?: '-'); ?></dd>
                    <dt class="col-5"><?php echo __('address'); ?></dt>
                    <dd class="col-7"><?php echo nl2br(htmlspecialchars($investor['address'] ?: '-')); ?></dd>
                    <dt class="col-5"><?php echo __('status'); ?></dt>
                    <dd class="col-7">
                        <span class="badge bg-<?php echo ($investor['status'] ?? 'active') === 'active' ? 'success' : 'secondary'; ?>"><?php echo htmlspecialchars(ucfirst((string)($investor['status'] ?? 'active'))); ?></span>
                    </dd>
                    <dt class="col-5">Joined</dt>
                    <dd class="col-7"><?php echo $investor['created_at'] ? htmlspecialchars(date('d M Y', strtotime($investor['created_at']))) : '-'; ?></dd>
                </dl>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card stat-card investor-card">
                    <div class="stat-icon"><i class="fas fa-chart-bar"></i></div>
                    <div class="stat-info">
                        <h3><?php echo inv_money($total_invested); ?></h3>
                        <p><?php echo __('total_investments'); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card">
                    <div class="stat-icon success"><i class="fas fa-hand-holding-usd"></i></div>
                    <div class="stat-info">
                        <h3><?php echo inv_money($total_interest); ?></h3>
                        <p><?php echo __('interest'); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card">
                    <div class="stat-icon primary"><i class="fas fa-wallet"></i></div>
                    <div class="stat-info">
                        <h3><?php echo count($accounts); ?></h3>
                        <p>Accounts</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-wallet me-2"></i>Investor Accounts</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th><?php echo __('account_number'); ?></th>
                                <th><?php echo __('investment_amount'); ?></th>
                                <th><?php echo __('interest_rate'); ?></th>
                                <th>Interest Earned</th>
                                <th><?php echo __('maturity_date'); ?></th>
                                <th><?php echo __('status'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($accounts)): ?>
                            <tr><td colspan="6" class="text-center text-muted py-4">No accounts yet.</td></tr>
                            <?php else: foreach ($accounts as $acc): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($acc['account_number']); ?></strong></td>
                                <td><?php echo inv_money($acc['investment_amount']); ?></td>
                                <td><?php echo number_format((float)$acc['interest_rate'], 2); ?>%</td>
                                <td class="text-success"><?php echo inv_money($acc['interest_earned']); ?></td>
                                <td><?php echo $acc['maturity_date'] ? htmlspecialchars(date('d M Y', strtotime($acc['maturity_date']))) : '-'; ?></td>
                                <td><span class="badge bg-<?php echo ($acc['status'] ?? 'active') === 'active' ? 'success' : 'secondary'; ?>"><?php echo htmlspecialchars(ucfirst((string)($acc['status'] ?? 'active'))); ?></span></td>
                            </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-exchange-alt me-2"></i>Recent <?php echo __('transactions'); ?></h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th><?php echo __('date'); ?></th>
                        <th><?php echo __('account_number'); ?></th>
                        <th>Type</th>
                        <th><?php echo __('amount'); ?></th>
                        <th>Balance After</th>
                        <th><?php echo __('description'); ?></th>
                        <th><?php echo __('reference'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($transactions)): ?>
                    <tr><td colspan="7" class="text-center text-muted py-4">No transactions yet.</td></tr>
                    <?php else: foreach ($transactions as $t): ?>
                    <tr>
                        <td><?php echo $t['created_at'] ? htmlspecialchars(date('d M Y H:i', strtotime($t['created_at']))) : '-'; ?></td>
                        <td><?php echo htmlspecialchars($t['account_number']); ?></td>
                        <td><span class="badge bg-info"><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', (string)$t['type']))); ?></span></td>
                        <td><?php echo inv_money($t['amount']); ?></td>
                        <td><?php echo inv_money($t['balance_after']); ?></td>
                        <td><?php echo htmlspecialchars($t['description'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($t['reference'] ?? ''); ?></td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<?php include $base_path . 'includes/footer.php'; ?>
