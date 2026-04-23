<?php
$base_path = '../';
$page_title = 'Record Payment';
$current_page = 'repayments';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$loan_id = (int)($_GET['loan_id'] ?? $_POST['loan_id'] ?? 0);
$loan = [];
$error = '';
$success = '';

if ($loan_id) {
    try {
        $stmt = $con->prepare("
            SELECT l.*, b.fname, b.lname, b.mobile_no,
                   COALESCE((SELECT SUM(amount) FROM loan_repayment WHERE loan_id = l.id), 0) as paid_amount
            FROM loans l
            LEFT JOIN borrower b ON l.borrower_id = b.id
            WHERE l.id = ?
        ");
        $stmt->execute([$loan_id]);
        $loan = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {}
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['loan_id'])) {
    $lid = (int)$_POST['loan_id'];
    $amount = (float)($_POST['amount'] ?? 0);
    $paid_date = $_POST['paid_date'] ?? date('Y-m-d');
    $principal = (float)($_POST['principal'] ?? 0);
    $interest = (float)($_POST['interest'] ?? 0);
    $notes = trim($_POST['notes'] ?? '');
    $collection_method = $_POST['collection_method'] ?? 'Cash';
    $receipt_no = trim($_POST['receipt_no'] ?? '');

    if ($amount <= 0) {
        $error = __('amount_must_be_greater_than_zero');
    } else {
        try {
            $stmt = $con->prepare("
                INSERT INTO loan_repayment (loan_id, amount, principal, interest, paid_date, notes, collection_method, receipt_no, created_by, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0, NOW())
            ");
            $stmt->execute([$lid, $amount, $principal, $interest, $paid_date, $notes, $collection_method, $receipt_no]);

            // Send SMS confirmation if mobile available
            $totalPaid = ($loan['paid_amount'] ?? 0) + $amount;
            $outstanding = ($loan['principal_amt'] ?? 0) - $totalPaid;
            if (!empty($loan['mobile_no'])) {
                try {
                    require_once $base_path . 'helpers/sms.php';
                    $smsMsg = "Payment of TZS " . number_format($amount, 2) . " received for loan " . ($loan['loan_no'] ?? $lid) . ". Outstanding balance: TZS " . number_format(max(0, $outstanding), 2) . ". Thank you - Loan Store";
                    sendSMS($loan['mobile_no'], $smsMsg);
                } catch (Exception $e) {}
            }

            header("Location: ../loans/view_loan_details.php?id=" . $lid . "&payment_success=1");
            exit;
        } catch (PDOException $e) {
            $error = 'Error recording payment: ' . $e->getMessage();
        }
    }
}

$outstanding = ($loan['principal_amt'] ?? 0) - ($loan['paid_amount'] ?? 0);

include $base_path . 'includes/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><?php echo __('record_payment'); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item"><a href="view_repayments.php"><?php echo __('repayments'); ?></a></li>
                <li class="breadcrumb-item active"><?php echo __('record_payment'); ?></li>
            </ol>
        </nav>
    </div>
    <?php if ($loan_id): ?>
    <a href="../loans/view_loan_details.php?id=<?php echo $loan_id; ?>" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i><?php echo __('back_to_loan'); ?></a>
    <?php else: ?>
    <a href="view_repayments.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i><?php echo __('back'); ?></a>
    <?php endif; ?>
</div>

<?php if ($error): ?>
<div class="alert alert-danger alert-dismissible fade show"><i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>

<div class="row g-4">
    <div class="col-lg-7">
        <form action="" method="POST">
            <input type="hidden" name="loan_id" value="<?php echo $loan_id; ?>">
            <div class="card">
                <div class="card-header"><h5><i class="fas fa-money-bill me-2"></i><?php echo __('payment_details'); ?></h5></div>
                <div class="card-body">
                    <?php if (!$loan_id || empty($loan)): ?>
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('loan'); ?> <span class="text-danger">*</span></label>
                        <select name="loan_id" class="form-select" required onchange="this.form.querySelector('[name=loan_id]').value=this.value; location='?loan_id='+this.value">
                            <option value="">-- <?php echo __('select_loan'); ?> --</option>
                            <?php
                            try {
                                $allLoans = $con->query("SELECT l.id, l.loan_no, b.fname, b.lname FROM loans l LEFT JOIN borrower b ON l.borrower_id = b.id WHERE l.status NOT IN ('paid','closed') ORDER BY l.id DESC LIMIT 200")->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($allLoans as $al) {
                                    echo '<option value="' . $al['id'] . '">' . htmlspecialchars(($al['loan_no'] ?? 'L-' . $al['id']) . ' - ' . $al['fname'] . ' ' . $al['lname']) . '</option>';
                                }
                            } catch (PDOException $e) {}
                            ?>
                        </select>
                    </div>
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label"><?php echo __('payment_amount'); ?> (TZS) <span class="text-danger">*</span></label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text">TZS</span>
                            <input type="number" name="amount" id="amountInput" class="form-control" step="0.01" min="0.01" value="<?php echo number_format(max(0, $outstanding), 2, '.', ''); ?>" required oninput="splitAmount(this.value)">
                        </div>
                        <?php if ($outstanding > 0): ?>
                        <small class="text-muted"><?php echo __('outstanding_balance'); ?>: TZS <?php echo number_format($outstanding, 2); ?></small>
                        <?php endif; ?>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label"><?php echo __('principal_portion'); ?> (TZS)</label>
                            <div class="input-group">
                                <span class="input-group-text">TZS</span>
                                <input type="number" name="principal" id="principalInput" class="form-control" step="0.01" min="0" value="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><?php echo __('interest_portion'); ?> (TZS)</label>
                            <div class="input-group">
                                <span class="input-group-text">TZS</span>
                                <input type="number" name="interest" id="interestInput" class="form-control" step="0.01" min="0" value="0">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><?php echo __('payment_date'); ?> <span class="text-danger">*</span></label>
                        <input type="date" name="paid_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><?php echo __('collection_method'); ?></label>
                        <select name="collection_method" class="form-select">
                            <option value="Cash"><?php echo __('cash'); ?></option>
                            <option value="Bank Transfer"><?php echo __('bank_transfer'); ?></option>
                            <option value="M-Pesa">M-Pesa</option>
                            <option value="Tigo Pesa">Tigo Pesa</option>
                            <option value="Airtel Money">Airtel Money</option>
                            <option value="Cheque">Cheque</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><?php echo __('receipt_no'); ?></label>
                        <input type="text" name="receipt_no" class="form-control" placeholder="RCP-<?php echo date('Ymd') . '-' . rand(1000,9999); ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><?php echo __('notes'); ?></label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="<?php echo __('optional_notes'); ?>"></textarea>
                    </div>

                    <button type="submit" class="btn btn-success w-100">
                        <i class="fas fa-save me-2"></i><?php echo __('record_payment'); ?>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <?php if ($loan): ?>
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header"><h5><i class="fas fa-info-circle me-2"></i><?php echo __('loan_summary'); ?></h5></div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode(($loan['fname'] ?? 'U') . '+' . ($loan['lname'] ?? 'N')); ?>&size=50&background=2c7be5&color=fff" class="rounded-circle me-3" style="width:50px;height:50px;">
                    <div>
                        <h6 class="mb-0"><?php echo htmlspecialchars(($loan['fname'] ?? '') . ' ' . ($loan['lname'] ?? '')); ?></h6>
                        <small class="text-muted"><?php echo htmlspecialchars($loan['mobile_no'] ?? ''); ?></small>
                    </div>
                </div>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between">
                    <span class="text-muted"><?php echo __('loan_no'); ?></span>
                    <strong><?php echo htmlspecialchars($loan['loan_no'] ?? 'L-' . $loan_id); ?></strong>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span class="text-muted"><?php echo __('principal_amount'); ?></span>
                    <strong>TZS <?php echo number_format($loan['principal_amt'] ?? 0, 2); ?></strong>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span class="text-muted"><?php echo __('total_paid'); ?></span>
                    <strong class="text-success">TZS <?php echo number_format($loan['paid_amount'] ?? 0, 2); ?></strong>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span class="text-muted"><?php echo __('outstanding'); ?></span>
                    <strong class="text-danger">TZS <?php echo number_format(max(0, $outstanding), 2); ?></strong>
                </li>
                <li class="list-group-item">
                    <div class="d-flex justify-content-between mb-1">
                        <small class="text-muted"><?php echo __('repayment_progress'); ?></small>
                        <?php $pct = ($loan['principal_amt'] ?? 0) > 0 ? round(($loan['paid_amount'] / $loan['principal_amt']) * 100, 1) : 0; ?>
                        <small><?php echo $pct; ?>%</small>
                    </div>
                    <div class="progress" style="height:8px;">
                        <div class="progress-bar bg-success" style="width:<?php echo $pct; ?>%"></div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
function splitAmount(total) {
    total = parseFloat(total) || 0;
    // Simple split - auto-fill principal as the full amount, user can adjust
    document.getElementById('principalInput').value = total.toFixed(2);
    document.getElementById('interestInput').value = '0.00';
}
</script>

<?php include $base_path . 'includes/footer.php'; ?>
