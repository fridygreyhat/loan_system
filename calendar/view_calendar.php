<?php
$base_path = '../';
$page_title = 'Calendar';
$current_page = 'calendar';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$current_month = isset($_GET['month']) ? intval($_GET['month']) : intval(date('m'));
$current_year = isset($_GET['year']) ? intval($_GET['year']) : intval(date('Y'));

$first_day = mktime(0, 0, 0, $current_month, 1, $current_year);
$days_in_month = date('t', $first_day);
$day_of_week = date('w', $first_day);

$prev_month = $current_month - 1;
$prev_year = $current_year;
if ($prev_month < 1) {
    $prev_month = 12;
    $prev_year--;
}

$next_month = $current_month + 1;
$next_year = $current_year;
if ($next_month > 12) {
    $next_month = 1;
    $next_year++;
}

$due_amounts = [];
$disbursed_amounts = [];
$payments = [];

try {
    $stmt = $con->prepare("SELECT DATE(due_date) as date, SUM(amount_due) as total FROM loan_schedule 
                           WHERE EXTRACT(MONTH FROM due_date) = ? AND EXTRACT(YEAR FROM due_date) = ?
                           GROUP BY DATE(due_date)");
    $stmt->execute([$current_month, $current_year]);
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $due_amounts[date('j', strtotime($row['date']))] = $row['total'];
    }
    
    $stmt = $con->prepare("SELECT DATE(creation_date) as date, SUM(principal_amount) as total FROM loans 
                           WHERE EXTRACT(MONTH FROM creation_date) = ? AND EXTRACT(YEAR FROM creation_date) = ?
                           GROUP BY DATE(creation_date)");
    $stmt->execute([$current_month, $current_year]);
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $disbursed_amounts[date('j', strtotime($row['date']))] = $row['total'];
    }
    
    $stmt = $con->prepare("SELECT DATE(paid_date) as date, SUM(amount) as total FROM loan_repayment 
                           WHERE EXTRACT(MONTH FROM paid_date) = ? AND EXTRACT(YEAR FROM paid_date) = ?
                           GROUP BY DATE(paid_date)");
    $stmt->execute([$current_month, $current_year]);
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $payments[date('j', strtotime($row['date']))] = $row['total'];
    }
} catch (PDOException $e) {
}

$month_names = [
    1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
    5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
    9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
];

$month_names_sw = [
    1 => 'Januari', 2 => 'Februari', 3 => 'Machi', 4 => 'Aprili',
    5 => 'Mei', 6 => 'Juni', 7 => 'Julai', 8 => 'Agosti',
    9 => 'Septemba', 10 => 'Oktoba', 11 => 'Novemba', 12 => 'Desemba'
];

$lang = get_current_lang();
$month_name = $lang === 'sw' ? $month_names_sw[$current_month] : $month_names[$current_month];

include $base_path . 'includes/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><i class="fas fa-calendar-alt me-2"></i><?php echo __('calendar'); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item active"><?php echo __('calendar'); ?></li>
            </ol>
        </nav>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="?month=<?php echo $prev_month; ?>&year=<?php echo $prev_year; ?>" class="btn btn-outline-primary">
                <i class="fas fa-chevron-left"></i> <?php echo __('previous'); ?>
            </a>
            <h4 class="mb-0"><?php echo $month_name . ' ' . $current_year; ?></h4>
            <a href="?month=<?php echo $next_month; ?>&year=<?php echo $next_year; ?>" class="btn btn-outline-primary">
                <?php echo __('next'); ?> <i class="fas fa-chevron-right"></i>
            </a>
        </div>
        
        <div class="row mb-3">
            <div class="col-md-4">
                <div class="d-flex align-items-center">
                    <span class="badge bg-danger me-2" style="width: 20px; height: 20px;"></span>
                    <span><?php echo __('due_amounts'); ?></span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="d-flex align-items-center">
                    <span class="badge bg-primary me-2" style="width: 20px; height: 20px;"></span>
                    <span><?php echo __('disbursed_amounts'); ?></span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="d-flex align-items-center">
                    <span class="badge bg-success me-2" style="width: 20px; height: 20px;"></span>
                    <span><?php echo __('payments_collected'); ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-bordered mb-0 calendar-table">
            <thead>
                <tr class="bg-light">
                    <th class="text-center"><?php echo __('sun'); ?></th>
                    <th class="text-center"><?php echo __('mon'); ?></th>
                    <th class="text-center"><?php echo __('tue'); ?></th>
                    <th class="text-center"><?php echo __('wed'); ?></th>
                    <th class="text-center"><?php echo __('thu'); ?></th>
                    <th class="text-center"><?php echo __('fri'); ?></th>
                    <th class="text-center"><?php echo __('sat'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $day = 1;
                while ($day <= $days_in_month) {
                    echo '<tr>';
                    for ($i = 0; $i < 7; $i++) {
                        if (($day == 1 && $i < $day_of_week) || $day > $days_in_month) {
                            echo '<td class="bg-light"></td>';
                        } else {
                            $is_today = ($day == date('j') && $current_month == date('m') && $current_year == date('Y'));
                            $today_class = $is_today ? 'bg-primary bg-opacity-10 border-primary' : '';
                            
                            echo '<td class="calendar-day ' . $today_class . '" style="height: 120px; vertical-align: top;">';
                            echo '<div class="d-flex justify-content-between align-items-start">';
                            echo '<span class="fw-bold ' . ($is_today ? 'text-primary' : '') . '">' . $day . '</span>';
                            if ($is_today) {
                                echo '<span class="badge bg-primary">' . __('today') . '</span>';
                            }
                            echo '</div>';
                            
                            if (isset($due_amounts[$day])) {
                                echo '<div class="small text-danger mt-1"><i class="fas fa-exclamation-circle me-1"></i>TZS ' . number_format($due_amounts[$day]) . '</div>';
                            }
                            if (isset($disbursed_amounts[$day])) {
                                echo '<div class="small text-primary mt-1"><i class="fas fa-hand-holding-usd me-1"></i>TZS ' . number_format($disbursed_amounts[$day]) . '</div>';
                            }
                            if (isset($payments[$day])) {
                                echo '<div class="small text-success mt-1"><i class="fas fa-check-circle me-1"></i>TZS ' . number_format($payments[$day]) . '</div>';
                            }
                            
                            echo '</td>';
                            $day++;
                        }
                    }
                    echo '</tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<style>
.calendar-table th, .calendar-table td {
    width: 14.28%;
}
.calendar-day:hover {
    background-color: #f8f9fa;
}
</style>

<?php include $base_path . 'includes/footer.php'; ?>
