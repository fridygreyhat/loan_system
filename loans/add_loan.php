<?php
$base_path = '../';
$page_title = 'Add Loan';
$current_page = 'loans';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$borrower_id = (int)($_GET['borrower'] ?? $_GET['borrower_id'] ?? 0);
$borrower = [];
$loan_products = [];
$loan_charges = [];
$disbursement_methods = [];
$error = '';
$success = '';

try {
    $loan_products = $con->query("SELECT * FROM loan_product WHERE active_flg = 1 ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) { $loan_products = []; }
try {
    $loan_charges = $con->query("SELECT * FROM loan_charges ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) { $loan_charges = []; }
try {
    $disbursement_methods = $con->query("SELECT * FROM disbursement_methods ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) { $disbursement_methods = []; }

if ($borrower_id) {
    try {
        $stmt = $con->prepare("SELECT * FROM borrower WHERE id = ?");
        $stmt->execute([$borrower_id]);
        $borrower = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) { $borrower = []; }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['loan_product_id'])) {
    $borrower_id_post = (int)($_POST['borrower_id'] ?? 0);
    $loan_product_id = (int)($_POST['loan_product_id'] ?? 0);
    $loan_no = trim($_POST['loan_application_id'] ?? '');
    $disbursement_mtd = $_POST['loan_disbursed_by_id'] ?? '';
    $principal_amt = (float)($_POST['loan_principal_amount'] ?? 0);
    $release_date = $_POST['loan_released_date'] ?? '';
    $interest_mtd = $_POST['loan_interest_method'] ?? 'flat_rate';
    $loan_interest = (float)($_POST['loan_interest'] ?? 0);
    $loan_interest_pd = $_POST['loan_interest_period'] ?? 'Month';
    $loan_duration = (int)($_POST['loan_duration'] ?? 1);
    $loan_duration_pd = $_POST['loan_duration_period'] ?? 'Months';
    $repayment_cycle = $_POST['loan_payment_scheme_id'] ?? 'Monthly';
    $no_repayment_cycle = (int)($_POST['loan_num_of_repayments'] ?? 1);
    $description = trim($_POST['loan_description'] ?? '');
    $status = $_POST['loan_status_id'] ?? 'pending_approval';

    if (!$loan_product_id || !$principal_amt || !$release_date) {
        $error = __('please_fill_required_fields');
    } else {
        try {
            $stmt = $con->prepare("
                INSERT INTO loans (loan_product_id, borrower_id, loan_no, disbursement_mtd, principal_amt, release_date,
                    interest_mtd, loan_interest, loan_interest_pd, loan_duration, loan_duration_pd, repayment_cycle,
                    no_repayment_cycle, description, status, creation_user, creation_date)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, NOW())
            ");
            $stmt->execute([
                $loan_product_id, $borrower_id_post, $loan_no, $disbursement_mtd, $principal_amt,
                $release_date, $interest_mtd, $loan_interest, $loan_interest_pd, $loan_duration,
                $loan_duration_pd, $repayment_cycle, $no_repayment_cycle, $description, $status
            ]);
            $new_loan_id = $con->lastInsertId();

            // Save applied charges
            if (!empty($_POST['loan_fee']) && is_array($_POST['loan_fee'])) {
                foreach ($_POST['loan_fee'] as $fee_id) {
                    try {
                        $feeStmt = $con->prepare("SELECT * FROM loan_charges WHERE id = ?");
                        $feeStmt->execute([$fee_id]);
                        $fee = $feeStmt->fetch(PDO::FETCH_ASSOC);
                        if ($fee) {
                            $feeAmount = $fee['amount'] ?? 0;
                            if ($fee['calculation_type'] === 'percentage') {
                                $feeAmount = $principal_amt * ($fee['amount'] / 100);
                            }
                            $chargeStmt = $con->prepare("INSERT INTO loan_applied_charges (loan_id, loan_charge_id, amount, creation_user) VALUES (?, ?, ?, 0)");
                            $chargeStmt->execute([$new_loan_id, $fee_id, $feeAmount]);
                        }
                    } catch (PDOException $e) {}
                }
            }

            header("Location: view_loan_details.php?id=" . $new_loan_id . "&success=1");
            exit;
        } catch (PDOException $e) {
            $error = 'Error saving loan: ' . $e->getMessage();
        }
    }
}

// Generate unique loan number
$next_loan_no = 'L-' . date('Y') . '-' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);

include $base_path . 'includes/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><?php echo __('add_loan'); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item"><a href="view_loans.php"><?php echo __('loans'); ?></a></li>
                <li class="breadcrumb-item active"><?php echo __('add_loan'); ?></li>
            </ol>
        </nav>
    </div>
    <a href="view_loans.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i><?php echo __('back'); ?></a>
</div>

<?php if ($error): ?>
<div class="alert alert-danger alert-dismissible fade show"><i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>

<form action="" method="POST" id="addLoanForm">
    <input type="hidden" name="borrower_id" value="<?php echo $borrower_id; ?>">
    <div class="row g-4">
        <div class="col-lg-8">
            <?php if ($borrower): ?>
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode(($borrower['fname'] ?? 'U') . '+' . ($borrower['lname'] ?? 'N')); ?>&size=50&background=2c7be5&color=fff" class="rounded-circle" style="width:50px;height:50px;">
                        <div>
                            <h6 class="mb-0"><?php echo htmlspecialchars(($borrower['title'] ?? '') . ' ' . ($borrower['fname'] ?? '') . ' ' . ($borrower['lname'] ?? '')); ?></h6>
                            <small class="text-muted"><?php echo htmlspecialchars($borrower['mobile_no'] ?? ''); ?> &bull; <?php echo htmlspecialchars($borrower['email'] ?? ''); ?></small>
                        </div>
                        <a href="../borrowers/view_borrowers.php" class="btn btn-sm btn-outline-secondary ms-auto"><?php echo __('change_borrower'); ?></a>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <div class="card mb-4">
                <div class="card-body">
                    <label class="form-label fw-semibold"><?php echo __('select_borrower'); ?> <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <select name="borrower_id_select" id="borrowerSelect" class="form-select" onchange="this.form.querySelector('[name=borrower_id]').value=this.value">
                            <option value="">-- <?php echo __('select_borrower'); ?> --</option>
                            <?php
                            try {
                                $allBorrowers = $con->query("SELECT id, fname, lname FROM borrower ORDER BY fname LIMIT 500")->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($allBorrowers as $b) {
                                    echo '<option value="' . $b['id'] . '">' . htmlspecialchars($b['fname'] . ' ' . $b['lname']) . '</option>';
                                }
                            } catch (PDOException $e) {}
                            ?>
                        </select>
                        <a href="../borrowers/add_borrower.php" class="btn btn-outline-primary" target="_blank"><?php echo __('add_new'); ?></a>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header"><h5><i class="fas fa-file-invoice-dollar me-2"></i><?php echo __('loan_details'); ?></h5></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label"><?php echo __('loan_product'); ?> <span class="text-danger">*</span></label>
                            <select name="loan_product_id" class="form-select" required>
                                <option value="">-- <?php echo __('select_product'); ?> --</option>
                                <?php foreach ($loan_products as $p): ?>
                                <option value="<?php echo $p['id']; ?>" <?php echo ($_POST['loan_product_id'] ?? '') == $p['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($p['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <small><a href="../admin/loan_products.php" target="_blank"><?php echo __('manage_products'); ?></a></small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><?php echo __('loan_no'); ?> <span class="text-danger">*</span></label>
                            <input type="text" name="loan_application_id" class="form-control" value="<?php echo htmlspecialchars($_POST['loan_application_id'] ?? $next_loan_no); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><?php echo __('status'); ?></label>
                            <select name="loan_status_id" class="form-select">
                                <option value="pending_approval"><?php echo __('pending_approval'); ?></option>
                                <option value="active" <?php echo ($_POST['loan_status_id'] ?? '') === 'active' ? 'selected' : ''; ?>><?php echo __('active'); ?></option>
                                <option value="processing" <?php echo ($_POST['loan_status_id'] ?? '') === 'processing' ? 'selected' : ''; ?>>Processing</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><?php echo __('disbursed_by'); ?></label>
                            <select name="loan_disbursed_by_id" class="form-select">
                                <option value="Cash"><?php echo __('cash'); ?></option>
                                <option value="Bank Transfer"><?php echo __('bank_transfer'); ?></option>
                                <option value="Mobile Money">Mobile Money (M-Pesa / Tigo Pesa)</option>
                                <?php foreach ($disbursement_methods as $dm): ?>
                                <option value="<?php echo htmlspecialchars($dm['name']); ?>"><?php echo htmlspecialchars($dm['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header"><h5><i class="fas fa-money-bill me-2"></i><?php echo __('principal'); ?></h5></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label"><?php echo __('principal_amount'); ?> (TZS) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">TZS</span>
                                <input type="number" name="loan_principal_amount" class="form-control" id="principalAmount" placeholder="0.00" step="0.01" min="0" value="<?php echo htmlspecialchars($_POST['loan_principal_amount'] ?? ''); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><?php echo __('release_date'); ?> <span class="text-danger">*</span></label>
                            <input type="date" name="loan_released_date" class="form-control" value="<?php echo htmlspecialchars($_POST['loan_released_date'] ?? date('Y-m-d')); ?>" required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header"><h5><i class="fas fa-percentage me-2"></i><?php echo __('interest'); ?></h5></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label"><?php echo __('interest_method'); ?></label>
                            <select name="loan_interest_method" class="form-select">
                                <option value="flat_rate"><?php echo __('flat_rate'); ?></option>
                                <option value="reducing_balance"><?php echo __('reducing_balance'); ?></option>
                                <option value="interest_only">Interest Only</option>
                                <option value="compound_interest">Compound Interest</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label"><?php echo __('interest_rate'); ?> (%)</label>
                            <input type="number" name="loan_interest" class="form-control" id="interestRate" placeholder="%" step="0.01" min="0" value="<?php echo htmlspecialchars($_POST['loan_interest'] ?? ''); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label"><?php echo __('interest_period'); ?></label>
                            <select name="loan_interest_period" class="form-select">
                                <option value="Day"><?php echo __('per_day'); ?></option>
                                <option value="Week"><?php echo __('per_week'); ?></option>
                                <option value="Month" selected><?php echo __('per_month'); ?></option>
                                <option value="Year"><?php echo __('per_year'); ?></option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header"><h5><i class="fas fa-calendar me-2"></i><?php echo __('duration_repayment'); ?></h5></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label"><?php echo __('loan_duration'); ?></label>
                            <input type="number" name="loan_duration" class="form-control" min="1" value="<?php echo htmlspecialchars($_POST['loan_duration'] ?? '12'); ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label"><?php echo __('duration_period'); ?></label>
                            <select name="loan_duration_period" class="form-select">
                                <option value="Days"><?php echo __('days'); ?></option>
                                <option value="Weeks"><?php echo __('weeks'); ?></option>
                                <option value="Months" selected><?php echo __('months'); ?></option>
                                <option value="Years"><?php echo __('years'); ?></option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label"><?php echo __('repayment_cycle'); ?></label>
                            <select name="loan_payment_scheme_id" class="form-select">
                                <option value="Daily"><?php echo __('daily'); ?></option>
                                <option value="Weekly"><?php echo __('weekly'); ?></option>
                                <option value="Bi-Weekly">Bi-Weekly</option>
                                <option value="Monthly" selected><?php echo __('monthly'); ?></option>
                                <option value="Quarterly">Quarterly</option>
                                <option value="Yearly"><?php echo __('yearly'); ?></option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label"><?php echo __('no_of_repayments'); ?></label>
                            <input type="number" name="loan_num_of_repayments" class="form-control" id="numRepayments" min="1" value="<?php echo htmlspecialchars($_POST['loan_num_of_repayments'] ?? '12'); ?>">
                        </div>
                    </div>
                </div>
            </div>

            <?php if (!empty($loan_charges)): ?>
            <div class="card mt-4">
                <div class="card-header"><h5><i class="fas fa-plus-circle me-2"></i><?php echo __('loan_fees_charges'); ?></h5></div>
                <div class="card-body">
                    <div class="row g-2">
                        <?php foreach ($loan_charges as $charge): ?>
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="loan_fee[]" value="<?php echo $charge['id']; ?>" id="charge_<?php echo $charge['id']; ?>">
                                <label class="form-check-label" for="charge_<?php echo $charge['id']; ?>">
                                    <?php echo htmlspecialchars($charge['name']); ?>
                                    <small class="text-muted">
                                        (<?php echo $charge['calculation_type'] === 'percentage' ? $charge['amount'] . '%' : 'TZS ' . number_format($charge['amount'], 2); ?>)
                                    </small>
                                </label>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="card mt-4">
                <div class="card-header"><h5><i class="fas fa-sticky-note me-2"></i><?php echo __('description'); ?></h5></div>
                <div class="card-body">
                    <textarea name="loan_description" class="form-control" rows="3" placeholder="<?php echo __('loan_description_placeholder'); ?>"><?php echo htmlspecialchars($_POST['loan_description'] ?? ''); ?></textarea>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card sticky-top" style="top:80px;">
                <div class="card-header"><h5><i class="fas fa-calculator me-2"></i><?php echo __('loan_summary'); ?></h5></div>
                <div class="card-body">
                    <div id="loanSummary">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted"><?php echo __('principal_amount'); ?></span>
                            <strong id="summPrincipal">TZS 0.00</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted"><?php echo __('total_interest'); ?></span>
                            <strong id="summInterest">TZS 0.00</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted"><?php echo __('no_of_repayments'); ?></span>
                            <strong id="summRepayments">-</strong>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <span class="fw-semibold"><?php echo __('per_repayment'); ?></span>
                            <strong class="text-primary" id="summPerPayment">TZS 0.00</strong>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary w-100 mb-2">
                        <i class="fas fa-save me-2"></i><?php echo __('save_loan'); ?>
                    </button>
                    <a href="view_loans.php" class="btn btn-outline-secondary w-100"><?php echo __('cancel'); ?></a>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-body">
                    <a href="loan_calculator.php" class="btn btn-outline-info w-100"><i class="fas fa-calculator me-2"></i><?php echo __('loan_calculator'); ?></a>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
function updateSummary() {
    const principal = parseFloat(document.querySelector('[name=loan_principal_amount]').value) || 0;
    const rate = parseFloat(document.querySelector('[name=loan_interest]').value) || 0;
    const numRep = parseInt(document.querySelector('[name=loan_num_of_repayments]').value) || 1;
    const totalInterest = principal * (rate / 100) * numRep;
    const perPayment = numRep > 0 ? (principal + totalInterest) / numRep : 0;
    document.getElementById('summPrincipal').textContent = 'TZS ' + principal.toLocaleString('en', {minimumFractionDigits:2});
    document.getElementById('summInterest').textContent = 'TZS ' + totalInterest.toLocaleString('en', {minimumFractionDigits:2});
    document.getElementById('summRepayments').textContent = numRep;
    document.getElementById('summPerPayment').textContent = 'TZS ' + perPayment.toLocaleString('en', {minimumFractionDigits:2});
}
document.querySelectorAll('[name=loan_principal_amount],[name=loan_interest],[name=loan_num_of_repayments]').forEach(el => {
    el.addEventListener('input', updateSummary);
});
updateSummary();
</script>

<?php include $base_path . 'includes/footer.php'; ?>
