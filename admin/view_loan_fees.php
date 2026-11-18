<?php
$base_path = '../';
$page_title = 'Loan Fees';
$current_page = 'admin';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$message = '';
$error = '';

// Loan fees are stored in loan_charges: charge_mtd is 'fixed' (TZS) or 'percentage' (% of principal).
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = (int)($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $type = ($_POST['type'] ?? 'fixed') === 'percentage' ? 'percentage' : 'fixed';
    $amount = (float)($_POST['amount'] ?? 0);

    try {
        if ($action === 'delete' && $id) {
            $inUse = $con->prepare("SELECT COUNT(*) FROM loan_applied_charges WHERE loan_charge_id = ?");
            $inUse->execute([$id]);
            if ($inUse->fetchColumn() > 0) {
                $error = 'This fee has been applied to loans and cannot be deleted.';
            } else {
                $con->prepare("DELETE FROM loan_charges WHERE id = ?")->execute([$id]);
                $message = 'Fee deleted.';
            }
        } elseif ($action === 'save') {
            if ($name === '' || $amount < 0 || ($type === 'percentage' && $amount > 100)) {
                $error = 'Enter a fee name and a valid amount (percentages must be between 0 and 100).';
            } elseif ($id) {
                $stmt = $con->prepare("UPDATE loan_charges SET name = ?, description = ?, charge_mtd = ?, charge_amount = ?, last_modified_by = ? WHERE id = ?");
                $stmt->execute([$name, $description, $type, $amount, $_SESSION['user_id'] ?? null, $id]);
                $message = 'Fee updated.';
            } else {
                $stmt = $con->prepare("INSERT INTO loan_charges (name, description, charge_mtd, charge_amount, creation_user) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$name, $description, $type, $amount, $_SESSION['user_id'] ?? 0]);
                $message = 'Fee added.';
            }
        }
    } catch (PDOException $e) {
        $error = 'Error saving fee: ' . $e->getMessage();
    }
}

$fees = [];
try {
    $fees = $con->query("SELECT id, name, description, COALESCE(charge_mtd, 'fixed') AS type, COALESCE(charge_amount, 0) AS amount FROM loan_charges ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = $error ?: 'Could not load fees: ' . $e->getMessage();
}

include $base_path . 'includes/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1>Loan Fees</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="index.php">Admin</a></li>
                <li class="breadcrumb-item active">Loan Fees</li>
            </ol>
        </nav>
    </div>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#feeModal" data-fee='{}'>
        <i class="fas fa-plus me-2"></i>Add Fee
    </button>
</div>

<?php if ($message): ?>
<div class="alert alert-success"><i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($message); ?></div>
<?php endif; ?>
<?php if ($error): ?>
<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-percentage me-2"></i>All Loan Fees</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Fee Name</th>
                        <th>Amount/Rate</th>
                        <th>Type</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($fees)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="fas fa-percentage fa-3x mb-3 d-block"></i>
                            No loan fees configured yet.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($fees as $index => $fee): ?>
                    <tr>
                        <td><?php echo $index + 1; ?></td>
                        <td><strong><?php echo htmlspecialchars($fee['name']); ?></strong></td>
                        <td>
                            <?php echo $fee['type'] === 'percentage'
                                ? rtrim(rtrim(number_format((float)$fee['amount'], 2), '0'), '.') . '%'
                                : 'TZS ' . number_format((float)$fee['amount'], 2); ?>
                        </td>
                        <td><?php echo $fee['type'] === 'percentage' ? 'Percentage of principal' : 'Fixed amount'; ?></td>
                        <td class="text-muted"><?php echo htmlspecialchars($fee['description'] ?? ''); ?></td>
                        <td class="text-nowrap">
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#feeModal"
                                    data-fee="<?php echo htmlspecialchars(json_encode($fee), ENT_QUOTES); ?>" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form method="POST" class="d-inline" onsubmit="return confirm('Delete this fee?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo (int)$fee['id']; ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="feeModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" class="modal-content">
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="id" id="feeId">
            <div class="modal-header">
                <h5 class="modal-title" id="feeModalTitle">Add Fee</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Fee Name *</label>
                    <input type="text" name="name" id="feeName" class="form-control" maxlength="50" required>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Type</label>
                        <select name="type" id="feeType" class="form-select">
                            <option value="fixed">Fixed amount (TZS)</option>
                            <option value="percentage">Percentage of principal</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Amount / Rate *</label>
                        <input type="number" name="amount" id="feeAmount" class="form-control" step="0.01" min="0" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <input type="text" name="description" id="feeDescription" class="form-control" maxlength="100">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('feeModal').addEventListener('show.bs.modal', function (e) {
    const fee = JSON.parse(e.relatedTarget.getAttribute('data-fee') || '{}');
    document.getElementById('feeModalTitle').textContent = fee.id ? 'Edit Fee' : 'Add Fee';
    document.getElementById('feeId').value = fee.id || '';
    document.getElementById('feeName').value = fee.name || '';
    document.getElementById('feeType').value = fee.type || 'fixed';
    document.getElementById('feeAmount').value = fee.amount || '';
    document.getElementById('feeDescription').value = fee.description || '';
});
</script>

<?php include $base_path . 'includes/footer.php'; ?>
