<?php
$base_path = '../';
$page_title = 'Edit Collateral';
$current_page = 'collateral';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$statuses = ['held' => 'Held', 'released' => 'Released', 'seized' => 'Seized', 'sold' => 'Sold'];
$types = ['Vehicle', 'Property', 'Equipment', 'Jewelry', 'Electronics', 'Livestock', 'Other'];

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$error = '';
$item = null;

if ($id > 0) {
    try {
        $stmt = $con->prepare("SELECT * FROM collateral WHERE id = ?");
        $stmt->execute([$id]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    } catch (PDOException $e) {
        error_log('edit_collateral load: ' . $e->getMessage());
        $error = 'Could not load the collateral: database error.';
    }
}

// Keep a legacy status value selectable so saving does not silently change it.
if ($item && !isset($statuses[$item['status']])) {
    $statuses[$item['status']] = ucfirst($item['status']);
}

$form = $item ? [
    'borrower_id' => (int)$item['borrower_id'],
    'loan_id' => (int)$item['loan_id'],
    'name' => $item['name'],
    'type' => $item['type'] ?? '',
    'value' => $item['value'],
    'serial_number' => $item['serial_number'] ?? '',
    'description' => $item['description'] ?? '',
    'status' => $item['status'],
] : null;

if ($item && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'update';

    if ($action === 'delete') {
        try {
            $stmt = $con->prepare("DELETE FROM collateral WHERE id = ?");
            $stmt->execute([$id]);
            header('Location: collateral_register.php');
            exit;
        } catch (PDOException $e) {
            error_log('edit_collateral delete: ' . $e->getMessage());
            $error = 'Could not delete the collateral: database error.';
        }
    } else {
        $form = [
            'borrower_id' => (int)($_POST['borrower_id'] ?? 0),
            'loan_id' => (int)($_POST['loan_id'] ?? 0),
            'name' => trim($_POST['name'] ?? ''),
            'type' => trim($_POST['type'] ?? ''),
            'value' => trim($_POST['value'] ?? ''),
            'serial_number' => trim($_POST['serial_number'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'status' => $_POST['status'] ?? 'held',
        ];
        $value = str_replace(',', '', (string)$form['value']);

        if ($form['borrower_id'] <= 0) {
            $error = 'Please select a borrower.';
        } elseif ($form['name'] === '') {
            $error = 'Collateral name is required.';
        } elseif ($value === '' || !is_numeric($value) || (float)$value < 0) {
            $error = 'Value must be a number of 0 or more.';
        } elseif (!isset($statuses[$form['status']])) {
            $error = 'Please select a valid status.';
        } else {
            try {
                $stmt = $con->prepare("SELECT id FROM borrower WHERE id = ?");
                $stmt->execute([$form['borrower_id']]);
                if (!$stmt->fetchColumn()) {
                    $error = 'Selected borrower was not found.';
                } elseif ($form['loan_id'] > 0) {
                    $stmt = $con->prepare("SELECT id FROM loans WHERE id = ? AND borrower_id = ?");
                    $stmt->execute([$form['loan_id'], $form['borrower_id']]);
                    if (!$stmt->fetchColumn()) {
                        $error = 'Selected loan does not belong to the selected borrower.';
                    }
                }

                if ($error === '') {
                    $stmt = $con->prepare("UPDATE collateral SET borrower_id = ?, loan_id = ?, name = ?, type = ?, value = ?,
                                               serial_number = ?, description = ?, status = ?
                                           WHERE id = ?");
                    $stmt->execute([
                        $form['borrower_id'],
                        $form['loan_id'] > 0 ? $form['loan_id'] : null,
                        $form['name'],
                        $form['type'] !== '' ? $form['type'] : null,
                        $value,
                        $form['serial_number'] !== '' ? $form['serial_number'] : null,
                        $form['description'] !== '' ? $form['description'] : null,
                        $form['status'],
                        $id,
                    ]);
                    header('Location: view_collateral.php?id=' . $id);
                    exit;
                }
            } catch (PDOException $e) {
                error_log('edit_collateral update: ' . $e->getMessage());
                $error = 'Could not save the collateral: database error.';
            }
        }
    }
}

$borrowers = [];
$loans = [];
if ($item) {
    try {
        $borrowers = $con->query("SELECT id, fname, lname, unique_no FROM borrower ORDER BY fname, lname")->fetchAll(PDO::FETCH_ASSOC);
        $loans = $con->query("SELECT id, borrower_id, loan_no, principal_amount, status FROM loans ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log('edit_collateral lists: ' . $e->getMessage());
        $error = $error ?: 'Could not load borrowers/loans: database error.';
    }
}

include $base_path . 'includes/header.php';
?>

<div class="content-header">
    <h1><i class="fas fa-edit text-primary me-2"></i>Edit Collateral</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
            <li class="breadcrumb-item"><a href="collateral_register.php"><?php echo __('collateral_register'); ?></a></li>
            <li class="breadcrumb-item active">Edit Collateral</li>
        </ol>
    </nav>
</div>

<?php if ($error): ?>
<div class="alert alert-danger alert-dismissible fade show">
    <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if (!$item): ?>
<?php if (!$error): ?>
<div class="alert alert-warning">
    <i class="fas fa-exclamation-triangle me-2"></i>Collateral not found.
    <a href="collateral_register.php" class="alert-link">Back to the collateral register</a>.
</div>
<?php endif; ?>
<?php else: ?>
<div class="row">
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Collateral Details</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="edit_collateral.php?id=<?php echo $id; ?>">
                    <input type="hidden" name="action" value="update">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Borrower <span class="text-danger">*</span></label>
                            <select name="borrower_id" id="borrowerSelect" class="form-select" required>
                                <option value="">-- Select Borrower --</option>
                                <?php foreach ($borrowers as $b): ?>
                                <option value="<?php echo (int)$b['id']; ?>" <?php echo $form['borrower_id'] === (int)$b['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars(trim($b['fname'] . ' ' . $b['lname']) . ($b['unique_no'] ? ' (' . $b['unique_no'] . ')' : '')); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Loan (optional)</label>
                            <select name="loan_id" id="loanSelect" class="form-select">
                                <option value="">-- No specific loan --</option>
                                <?php foreach ($loans as $l): ?>
                                <option value="<?php echo (int)$l['id']; ?>" data-borrower="<?php echo (int)$l['borrower_id']; ?>" <?php echo $form['loan_id'] === (int)$l['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars(($l['loan_no'] ?: '#' . $l['id']) . ' - TZS ' . number_format((float)$l['principal_amount'], 2) . ($l['status'] ? ' (' . $l['status'] . ')' : '')); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Only loans of the selected borrower are listed.</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required value="<?php echo htmlspecialchars($form['name']); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Type</label>
                            <input type="text" name="type" class="form-control" list="collateralTypes" value="<?php echo htmlspecialchars($form['type']); ?>">
                            <datalist id="collateralTypes">
                                <?php foreach ($types as $t): ?>
                                <option value="<?php echo htmlspecialchars($t); ?>">
                                <?php endforeach; ?>
                            </datalist>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Value <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">TZS</span>
                                <input type="number" name="value" class="form-control" min="0" step="0.01" required value="<?php echo htmlspecialchars((string)$form['value']); ?>">
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Serial / Registration No.</label>
                            <input type="text" name="serial_number" class="form-control" value="<?php echo htmlspecialchars($form['serial_number']); ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <?php foreach ($statuses as $key => $label): ?>
                                <option value="<?php echo htmlspecialchars($key); ?>" <?php echo $form['status'] === $key ? 'selected' : ''; ?>><?php echo htmlspecialchars($label); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars($form['description']); ?></textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Save Changes</button>
                        <a href="view_collateral.php?id=<?php echo $id; ?>" class="btn btn-outline-secondary"><i class="fas fa-times me-2"></i>Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-danger">
            <div class="card-header text-danger"><h5 class="mb-0"><i class="fas fa-trash me-2"></i>Delete Collateral</h5></div>
            <div class="card-body">
                <p class="small text-muted">This permanently removes the collateral record. This cannot be undone.</p>
                <form method="POST" action="edit_collateral.php?id=<?php echo $id; ?>" onsubmit="return confirm('Delete this collateral record?');">
                    <input type="hidden" name="action" value="delete">
                    <button type="submit" class="btn btn-outline-danger"><i class="fas fa-trash me-2"></i>Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    var borrower = document.getElementById('borrowerSelect');
    var loan = document.getElementById('loanSelect');
    if (!borrower || !loan) return;
    function filterLoans() {
        var b = borrower.value;
        Array.prototype.forEach.call(loan.options, function (opt) {
            if (!opt.dataset.borrower) return;
            var show = b !== '' && opt.dataset.borrower === b;
            opt.hidden = !show;
            opt.disabled = !show;
            if (!show && opt.selected) loan.value = '';
        });
    }
    borrower.addEventListener('change', filterLoans);
    filterLoans();
})();
</script>
<?php endif; ?>

<?php include $base_path . 'includes/footer.php'; ?>
