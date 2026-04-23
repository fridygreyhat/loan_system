<?php
$base_path = '../';
$page_title = 'Loan Calculator';
$current_page = 'loans';

require_once $base_path . 'helpers/lang.php';

include $base_path . 'includes/header.php';
?>

<div class="page-header">
    <h1><i class="fas fa-calculator me-2"></i><?php echo __('loan_calculator'); ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
            <li class="breadcrumb-item active"><?php echo __('loan_calculator'); ?></li>
        </ol>
    </nav>
</div>

<div class="row g-4">
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-sliders-h me-2"></i><?php echo __('loan_parameters'); ?></h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold"><?php echo __('interest_method'); ?></label>
                    <select id="calcMethod" class="form-select" onchange="calculate()">
                        <option value="flat_rate"><?php echo __('flat_rate'); ?></option>
                        <option value="reducing_balance"><?php echo __('reducing_balance'); ?></option>
                        <option value="compound_interest">Compound Interest</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold"><?php echo __('principal_amount'); ?> (TZS)</label>
                    <div class="input-group">
                        <span class="input-group-text">TZS</span>
                        <input type="number" id="calcPrincipal" class="form-control" value="100000" min="0" step="1000" oninput="calculate()">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold"><?php echo __('interest_rate'); ?> (%)</label>
                    <div class="row g-2">
                        <div class="col-6">
                            <input type="number" id="calcRate" class="form-control" value="5" min="0" step="0.1" oninput="calculate()">
                        </div>
                        <div class="col-6">
                            <select id="calcRatePeriod" class="form-select" onchange="calculate()">
                                <option value="day"><?php echo __('per_day'); ?></option>
                                <option value="week"><?php echo __('per_week'); ?></option>
                                <option value="month" selected><?php echo __('per_month'); ?></option>
                                <option value="year"><?php echo __('per_year'); ?></option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold"><?php echo __('loan_duration'); ?></label>
                    <div class="row g-2">
                        <div class="col-6">
                            <input type="number" id="calcDuration" class="form-control" value="12" min="1" oninput="calculate()">
                        </div>
                        <div class="col-6">
                            <select id="calcDurationPeriod" class="form-select" onchange="calculate()">
                                <option value="days"><?php echo __('days'); ?></option>
                                <option value="weeks"><?php echo __('weeks'); ?></option>
                                <option value="months" selected><?php echo __('months'); ?></option>
                                <option value="years"><?php echo __('years'); ?></option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold"><?php echo __('repayment_cycle'); ?></label>
                    <select id="calcRepaymentCycle" class="form-select" onchange="calculate()">
                        <option value="daily"><?php echo __('daily'); ?></option>
                        <option value="weekly"><?php echo __('weekly'); ?></option>
                        <option value="biweekly">Bi-Weekly</option>
                        <option value="monthly" selected><?php echo __('monthly'); ?></option>
                        <option value="quarterly">Quarterly</option>
                        <option value="yearly"><?php echo __('yearly'); ?></option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold"><?php echo __('loan_release_date'); ?></label>
                    <input type="date" id="calcReleaseDate" class="form-control" value="<?php echo date('Y-m-d'); ?>" onchange="calculate()">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold"><?php echo __('loan_fees'); ?> (TZS)</label>
                    <input type="number" id="calcFees" class="form-control" value="0" min="0" step="100" oninput="calculate()">
                    <small class="text-muted"><?php echo __('optional_fees_hint'); ?></small>
                </div>

                <button class="btn btn-primary w-100" onclick="calculate()">
                    <i class="fas fa-calculator me-2"></i><?php echo __('calculate'); ?>
                </button>
                <a href="add_loan.php" class="btn btn-outline-success w-100 mt-2">
                    <i class="fas fa-plus me-2"></i><?php echo __('add_loan'); ?>
                </a>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card mb-4">
            <div class="card-header"><h5><i class="fas fa-chart-bar me-2"></i><?php echo __('loan_summary'); ?></h5></div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-6 col-md-3">
                        <div class="text-center p-3 bg-light rounded">
                            <div class="text-primary fw-bold fs-5" id="resPrincipal">TZS 0</div>
                            <small class="text-muted"><?php echo __('principal'); ?></small>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="text-center p-3 bg-light rounded">
                            <div class="text-warning fw-bold fs-5" id="resInterest">TZS 0</div>
                            <small class="text-muted"><?php echo __('total_interest'); ?></small>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="text-center p-3 bg-light rounded">
                            <div class="text-info fw-bold fs-5" id="resFees">TZS 0</div>
                            <small class="text-muted"><?php echo __('fees'); ?></small>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="text-center p-3 bg-success bg-opacity-10 rounded border border-success">
                            <div class="text-success fw-bold fs-5" id="resTotal">TZS 0</div>
                            <small class="text-muted"><?php echo __('total_repayable'); ?></small>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row g-2 text-center">
                    <div class="col-4">
                        <small class="text-muted d-block"><?php echo __('no_of_repayments'); ?></small>
                        <strong id="resNumPayments">-</strong>
                    </div>
                    <div class="col-4">
                        <small class="text-muted d-block"><?php echo __('per_repayment'); ?></small>
                        <strong class="text-primary" id="resPerPayment">TZS 0</strong>
                    </div>
                    <div class="col-4">
                        <small class="text-muted d-block">APR</small>
                        <strong id="resAPR">0%</strong>
                    </div>
                </div>

                <div class="mt-3">
                    <canvas id="loanChart" height="150"></canvas>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-list me-2"></i><?php echo __('repayment_schedule'); ?></h5>
                <button class="btn btn-sm btn-outline-secondary" onclick="printSchedule()"><i class="fas fa-print me-1"></i><?php echo __('print'); ?></button>
            </div>
            <div class="card-body p-0" style="max-height:400px;overflow-y:auto;">
                <table class="table table-sm table-hover mb-0">
                    <thead class="table-light sticky-top">
                        <tr>
                            <th>#</th>
                            <th><?php echo __('due_date'); ?></th>
                            <th><?php echo __('principal'); ?></th>
                            <th><?php echo __('interest'); ?></th>
                            <th><?php echo __('balance'); ?></th>
                            <th><?php echo __('total_due'); ?></th>
                        </tr>
                    </thead>
                    <tbody id="scheduleBody">
                        <tr><td colspan="6" class="text-center text-muted py-3"><?php echo __('enter_params_to_calculate'); ?></td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
let loanChart = null;

function fmt(n) {
    return 'TZS ' + parseFloat(n).toLocaleString('en', {minimumFractionDigits:2, maximumFractionDigits:2});
}

function getNumRepayments(duration, durationPeriod, cycle) {
    let durationMonths = duration;
    if (durationPeriod === 'days') durationMonths = duration / 30;
    else if (durationPeriod === 'weeks') durationMonths = duration * 7 / 30;
    else if (durationPeriod === 'years') durationMonths = duration * 12;

    const cycleMap = { daily: 1/30, weekly: 7/30, biweekly: 14/30, monthly: 1, quarterly: 3, yearly: 12 };
    const cycleMonths = cycleMap[cycle] || 1;
    return Math.max(1, Math.round(durationMonths / cycleMonths));
}

function getDaysInCycle(cycle) {
    const map = { daily: 1, weekly: 7, biweekly: 14, monthly: 30, quarterly: 91, yearly: 365 };
    return map[cycle] || 30;
}

function addCycleToDate(date, cycle, n = 1) {
    const d = new Date(date);
    if (cycle === 'daily') d.setDate(d.getDate() + n);
    else if (cycle === 'weekly') d.setDate(d.getDate() + 7 * n);
    else if (cycle === 'biweekly') d.setDate(d.getDate() + 14 * n);
    else if (cycle === 'monthly') d.setMonth(d.getMonth() + n);
    else if (cycle === 'quarterly') d.setMonth(d.getMonth() + 3 * n);
    else if (cycle === 'yearly') d.setFullYear(d.getFullYear() + n);
    return d;
}

function calculate() {
    const method = document.getElementById('calcMethod').value;
    const principal = parseFloat(document.getElementById('calcPrincipal').value) || 0;
    const rate = parseFloat(document.getElementById('calcRate').value) || 0;
    const ratePeriod = document.getElementById('calcRatePeriod').value;
    const duration = parseInt(document.getElementById('calcDuration').value) || 1;
    const durationPeriod = document.getElementById('calcDurationPeriod').value;
    const cycle = document.getElementById('calcRepaymentCycle').value;
    const releaseDate = document.getElementById('calcReleaseDate').value;
    const fees = parseFloat(document.getElementById('calcFees').value) || 0;

    if (principal <= 0) return;

    const numPayments = getNumRepayments(duration, durationPeriod, cycle);

    // Convert rate to per-cycle rate
    const daysPerCycle = getDaysInCycle(cycle);
    let ratePerCycle;
    if (ratePeriod === 'day') ratePerCycle = rate * daysPerCycle / 100;
    else if (ratePeriod === 'week') ratePerCycle = rate * daysPerCycle / 7 / 100;
    else if (ratePeriod === 'month') ratePerCycle = rate * daysPerCycle / 30 / 100;
    else ratePerCycle = rate * daysPerCycle / 365 / 100;

    let schedule = [];
    let totalInterest = 0;

    if (method === 'flat_rate') {
        const interestPerCycle = principal * ratePerCycle;
        const principalPerPayment = principal / numPayments;
        const totalPerPayment = principalPerPayment + interestPerCycle;
        let balance = principal;
        let date = new Date(releaseDate);
        for (let i = 1; i <= numPayments; i++) {
            date = addCycleToDate(releaseDate, cycle, i);
            balance -= principalPerPayment;
            totalInterest += interestPerCycle;
            schedule.push({ n: i, date, principal: principalPerPayment, interest: interestPerCycle, balance: Math.max(0, balance), total: totalPerPayment });
        }
    } else if (method === 'reducing_balance') {
        const r = ratePerCycle;
        const paymentPerCycle = (r === 0) ? principal / numPayments : principal * r * Math.pow(1 + r, numPayments) / (Math.pow(1 + r, numPayments) - 1);
        let balance = principal;
        let date = new Date(releaseDate);
        for (let i = 1; i <= numPayments; i++) {
            date = addCycleToDate(releaseDate, cycle, i);
            const interest = balance * r;
            const princ = paymentPerCycle - interest;
            balance -= princ;
            totalInterest += interest;
            schedule.push({ n: i, date, principal: princ, interest, balance: Math.max(0, balance), total: paymentPerCycle });
        }
    } else { // compound
        const r = ratePerCycle;
        const totalRepayable = principal * Math.pow(1 + r, numPayments);
        const paymentPerCycle = totalRepayable / numPayments;
        totalInterest = totalRepayable - principal;
        let balance = principal;
        for (let i = 1; i <= numPayments; i++) {
            const date2 = addCycleToDate(releaseDate, cycle, i);
            const interest = balance * r;
            const princ = paymentPerCycle - interest;
            balance -= princ;
            schedule.push({ n: i, date: date2, principal: princ, interest, balance: Math.max(0, balance), total: paymentPerCycle });
        }
    }

    const perPayment = schedule.length > 0 ? schedule[0].total : 0;
    const totalRepayable = principal + totalInterest + fees;
    const apr = numPayments > 0 ? (totalInterest / principal) * (365 / (getDaysInCycle(cycle) * numPayments)) * 100 : 0;

    document.getElementById('resPrincipal').textContent = fmt(principal);
    document.getElementById('resInterest').textContent = fmt(totalInterest);
    document.getElementById('resFees').textContent = fmt(fees);
    document.getElementById('resTotal').textContent = fmt(totalRepayable);
    document.getElementById('resNumPayments').textContent = numPayments;
    document.getElementById('resPerPayment').textContent = fmt(perPayment + (fees / numPayments));
    document.getElementById('resAPR').textContent = apr.toFixed(1) + '%';

    // Draw chart
    const ctx = document.getElementById('loanChart').getContext('2d');
    if (loanChart) loanChart.destroy();
    loanChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Principal', 'Interest', 'Fees'],
            datasets: [{ data: [principal, totalInterest, fees], backgroundColor: ['#2c7be5','#e67e22','#27ae60'] }]
        },
        options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
    });

    // Render schedule
    const tbody = document.getElementById('scheduleBody');
    tbody.innerHTML = '';
    schedule.forEach(row => {
        const dateStr = row.date instanceof Date ? row.date.toLocaleDateString('en-GB', { day:'2-digit', month:'short', year:'numeric' }) : '';
        const tr = document.createElement('tr');
        tr.innerHTML = `<td>${row.n}</td><td>${dateStr}</td><td>${fmt(row.principal)}</td><td class="text-warning">${fmt(row.interest)}</td><td>${fmt(row.balance)}</td><td class="fw-semibold">${fmt(row.total)}</td>`;
        tbody.appendChild(tr);
    });
}

function printSchedule() {
    window.print();
}

// Calculate on load
calculate();
</script>

<?php include $base_path . 'includes/footer.php'; ?>
