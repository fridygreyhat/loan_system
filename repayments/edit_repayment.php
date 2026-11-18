<?php
$base_path = '../';
$page_title = 'Edit Repayment';
$current_page = 'repayments';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$repayment_id = (int)($_GET['id'] ?? 0);

$rep = null;
$error = '';
$payment_methods = ['Cash', 'Bank Transfer', 'M-Pesa', 'Tigo Pesa', 'Airtel Money', 'Cheque'];

if ($repayment_id > 0) {
    try {
        $stmt = $con->prepare("
            SELECT lr.*, l.loan_no, l.principal_amount, b.fname, b.lname
            FROM loan_repayment lr
            LEFT JOIN loans l ON l.id = lr.loan_id
            LEFT JOIN borrower b ON b.id = l.borrower_id
            WHERE lr.id = ?
        ");
        $stmt->execute([$repayment_id]);
        $rep = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    } catch (PDOException $e) {
        $error = 'Could not load repayment: ' . $e->getMessage();
    }
}

$form = $rep ?? [];

if ($rep && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $form = array_merge($form, [
        'amount'         => trim($_POST['amount'] ?? ''),
        'principal'      => trim($_POST['principal'] ?? ''),
        'interest'       => trim($_POST['interest'] ?? ''),
        'paid_date'      => trim($_POST['paid_date'] ?? ''),
        'payment_method' => trim($_POST['payment_method'] ?? ''),
        'receipt_no'     => trim($_POST['receipt_no'] ?? ''),
        'notes'          => trim($_POST['notes'] ?? ''),
    ]);
    $principal = $form['principal'] === '' ? '0' : $form['principal'];
    $interest = $form['interest'] === '' ? '0' : $form['interest'];
    $dt = DateTime::createFromFormat('!Y-m-d', $form['paid_date']);

    if (!is_numeric($form['amount']) || (float)$form['amount'] <= 0) {
        $error = 'Amount must be a number greater than zero.';
    } elseif (!is_numeric($principal) || (float)$principal < 0 || !is_numeric($interest) || (float)$interest < 0) {
        $error = 'Principal and interest must be non-negative numbers.';
    } elseif ((float)$principal + (float)$interest > (float)$form['amount'] + 0.005) {
        $error = 'Principal plus interest cannot exceed the amount paid.';
    } elseif (!$dt || $dt->format('Y-m-d') !== $form['paid_date']) {
        $error = 'Please enter a valid payment date.';
    } else {
        try {
            $stmt = $con->prepare("
                UPDATE loan_repayment
                SET amount = ?, principal = ?, interest = ?, paid_date = ?, payment_method = ?, receipt_no = ?, notes = ?
                WHERE id = ?
            ");
            $stmt->execute([
                $form['amount'],
                $principal,
                $interest,
                $form['paid_date'],
                $form['payment_method'] !== '' ? $form['payment_method'] : null,
                $form['receipt_no'] !== '' ? $form['receipt_no'] : null,
                $form['notes'] !== '' ? $form['notes'] : null,
                $repayment_id,
            ]);
            header('Location: view_repayments.php');
            exit;
        } catch (PDOException $e) {
            $error = 'Could not save repayment: ' . $e->getMessage();
        }
    }
}

$method_options = $payment_methods;
if (!empty($form['payment_method']) && !in_array($form['payment_method'], $method_options, true)) {
    array_unshift($method_options, $form['payment_method']); // preserve a legacy value
}

include $base_path . 'includes/header.php';
?>

<div class="page-header">
    <div>
        <h1><i class="fas fa-edit text-primary me-2"></i>Edit Repayment</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item"><a href="view_repayments.php"><?php echo __('repayments'); ?></a></li>
                <li class="breadcrumb-item active"><?php echo __('edit'); ?></li>
            </ol>
        </nav>
    </div>
</div>

<?php if ($error): ?>
<div class="alert alert-danger alert-dismissible fade show">
    <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if (!$rep): ?>
<?php if (!$error): ?>
<div class="alert alert-warning"><i class="fas fa-exclamation-triangle me-2"></i>Repayment not found. <a href="view_repayments.php">Back to repayments</a></div>
<?php endif; ?>
<?php else: ?>
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5>
                    <i class="fas fa-money-bill-wave me-2"></i>
                    Loan <a href="../loans/view_loan_details.php?id=<?php echo (int)$rep['loan_id']; ?>"><?php echo htmlspecialchars($rep['loan_no'] ?: ('L-' . $rep['loan_id'])); ?></a>
                    &middot; <?php echo htmlspecialchars(trim(($rep['fname'] ?? '') . ' ' . ($rep['lname'] ?? ''))); ?>
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="edit_repayment.php?id=<?php echo $repayment_id; ?>">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label"><?php echo __('amount'); ?> (TZS) <span class="text-danger">*</span></label>
                            <input type="number" name="amount" class="form-control" step="0.01" min="0.01" required value="<?php echo htmlspecialchars((string)$form['amount']); ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label"><?php echo __('principal'); ?></label>
                            <input type="number" name="principal" class="form-control" step="0.01" min="0" value="<?php echo htmlspecialchars((string)$form['principal']); ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label"><?php echo __('interest'); ?></label>
                            <input type="number" name="interest" class="form-control" step="0.01" min="0" value="<?php echo htmlspecialchars((string)$form['interest']); ?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label"><?php echo __('date'); ?> <span class="text-danger">*</span></label>
                            <input type="date" name="paid_date" class="form-control" required value="<?php echo htmlspecialchars((string)$form['paid_date']); ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label"><?php echo __('payment_method'); ?></label>
                            <select name="payment_method" class="form-select">
                                <?php foreach ($method_options as $m): ?>
                                <option value="<?php echo htmlspecialchars($m); ?>" <?php echo ($form['payment_method'] ?? '') === $m ? 'selected' : ''; ?>><?php echo htmlspecialchars($m); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label"><?php echo __('receipt_no'); ?></label>
                            <input type="text" name="receipt_no" class="form-control" value="<?php echo htmlspecialchars((string)($form['receipt_no'] ?? '')); ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('notes'); ?></label>
                        <textarea name="notes" class="form-control" rows="3"><?php echo htmlspecialchars((string)($form['notes'] ?? '')); ?></textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i><?php echo __('save_changes'); ?></button>
                        <a href="view_repayments.php" class="btn btn-outline-secondary"><i class="fas fa-times me-2"></i><?php echo __('cancel'); ?></a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php include $base_path . 'includes/footer.php'; ?>
