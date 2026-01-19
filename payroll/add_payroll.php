<?php
$base_path = '../';
$page_title = 'Add Payroll';
$current_page = 'payroll';
include '../includes/header.php';
require_once '../helpers/connect.php';

$db = $con;

$staff_list = [];
try {
    $stmt = $db->query("SELECT id, CONCAT(first_name, ' ', last_name) as full_name FROM staff WHERE status = 'active' ORDER BY first_name");
    $staff_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $staff_list = [];
}
?>

<div class="content-header">
    <h1><i class="fas fa-plus-circle"></i> <?php echo __('add_payroll'); ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
            <li class="breadcrumb-item"><a href="view_payroll.php"><?php echo __('payroll'); ?></a></li>
            <li class="breadcrumb-item active"><?php echo __('add_payroll'); ?></li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><?php echo __('payroll_details'); ?></h5>
            </div>
            <div class="card-body">
                <form action="save_payroll.php" method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?php echo __('staff_member'); ?> *</label>
                            <select name="staff_id" class="form-select" required>
                                <option value=""><?php echo __('select_staff'); ?></option>
                                <?php foreach ($staff_list as $staff): ?>
                                <option value="<?php echo $staff['id']; ?>"><?php echo htmlspecialchars($staff['full_name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?php echo __('pay_period'); ?> *</label>
                            <input type="month" name="pay_period" class="form-control" value="<?php echo date('Y-m'); ?>" required>
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    <h6 class="mb-3"><?php echo __('earnings'); ?></h6>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?php echo __('basic_salary'); ?> *</label>
                            <div class="input-group">
                                <span class="input-group-text">TZS</span>
                                <input type="number" name="basic_salary" class="form-control" value="0" required>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?php echo __('housing_allowance'); ?></label>
                            <div class="input-group">
                                <span class="input-group-text">TZS</span>
                                <input type="number" name="housing_allowance" class="form-control" value="0">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?php echo __('transport_allowance'); ?></label>
                            <div class="input-group">
                                <span class="input-group-text">TZS</span>
                                <input type="number" name="transport_allowance" class="form-control" value="0">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?php echo __('other_allowances'); ?></label>
                            <div class="input-group">
                                <span class="input-group-text">TZS</span>
                                <input type="number" name="other_allowances" class="form-control" value="0">
                            </div>
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    <h6 class="mb-3"><?php echo __('deductions'); ?></h6>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label"><?php echo __('tax'); ?> (PAYE)</label>
                            <div class="input-group">
                                <span class="input-group-text">TZS</span>
                                <input type="number" name="tax" class="form-control" value="0">
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label"><?php echo __('nssf'); ?></label>
                            <div class="input-group">
                                <span class="input-group-text">TZS</span>
                                <input type="number" name="nssf" class="form-control" value="0">
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label"><?php echo __('loan_deduction'); ?></label>
                            <div class="input-group">
                                <span class="input-group-text">TZS</span>
                                <input type="number" name="loan_deduction" class="form-control" value="0">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?php echo __('other_deductions'); ?></label>
                            <div class="input-group">
                                <span class="input-group-text">TZS</span>
                                <input type="number" name="other_deductions" class="form-control" value="0">
                            </div>
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?php echo __('payment_method'); ?></label>
                            <select name="payment_method" class="form-select">
                                <option value="bank_transfer"><?php echo __('bank_transfer'); ?></option>
                                <option value="cash"><?php echo __('cash'); ?></option>
                                <option value="mobile_money"><?php echo __('mobile_money'); ?></option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?php echo __('notes'); ?></label>
                            <textarea name="notes" class="form-control" rows="1"></textarea>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> <?php echo __('save_payroll'); ?>
                        </button>
                        <a href="view_payroll.php" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i> <?php echo __('cancel'); ?>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><?php echo __('payroll_summary'); ?></h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span><?php echo __('gross_earnings'); ?>:</span>
                    <strong id="grossEarnings">TZS 0</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span><?php echo __('total_deductions'); ?>:</span>
                    <strong id="totalDeductions" class="text-danger">TZS 0</strong>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <span class="h5"><?php echo __('net_pay'); ?>:</span>
                    <strong id="netPay" class="h5 text-success">TZS 0</strong>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const inputs = form.querySelectorAll('input[type="number"]');
    
    function calculatePayroll() {
        const basicSalary = parseFloat(form.querySelector('[name="basic_salary"]').value) || 0;
        const housingAllowance = parseFloat(form.querySelector('[name="housing_allowance"]').value) || 0;
        const transportAllowance = parseFloat(form.querySelector('[name="transport_allowance"]').value) || 0;
        const otherAllowances = parseFloat(form.querySelector('[name="other_allowances"]').value) || 0;
        
        const tax = parseFloat(form.querySelector('[name="tax"]').value) || 0;
        const nssf = parseFloat(form.querySelector('[name="nssf"]').value) || 0;
        const loanDeduction = parseFloat(form.querySelector('[name="loan_deduction"]').value) || 0;
        const otherDeductions = parseFloat(form.querySelector('[name="other_deductions"]').value) || 0;
        
        const grossEarnings = basicSalary + housingAllowance + transportAllowance + otherAllowances;
        const totalDeductions = tax + nssf + loanDeduction + otherDeductions;
        const netPay = grossEarnings - totalDeductions;
        
        document.getElementById('grossEarnings').textContent = 'TZS ' + grossEarnings.toLocaleString();
        document.getElementById('totalDeductions').textContent = 'TZS ' + totalDeductions.toLocaleString();
        document.getElementById('netPay').textContent = 'TZS ' + netPay.toLocaleString();
    }
    
    inputs.forEach(input => {
        input.addEventListener('input', calculatePayroll);
    });
});
</script>

<?php include '../includes/footer.php'; ?>
