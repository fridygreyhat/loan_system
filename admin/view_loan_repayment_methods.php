<?php
$base_path = '../';
$page_title = 'Loan Repayment Methods';
$current_page = 'admin';

require_once $base_path . 'helpers/connect.php';

$methods = [
    ['id' => 1, 'name' => 'Cash', 'status' => 'Active'],
    ['id' => 2, 'name' => 'Bank Transfer', 'status' => 'Active'],
    ['id' => 3, 'name' => 'Mobile Money', 'status' => 'Active'],
    ['id' => 4, 'name' => 'Cheque', 'status' => 'Active'],
    ['id' => 5, 'name' => 'Debit Card', 'status' => 'Active'],
];

include $base_path . 'includes/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1>Loan Repayment Methods</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="index.php">Admin</a></li>
                <li class="breadcrumb-item active">Repayment Methods</li>
            </ol>
        </nav>
    </div>
    <a href="add_loan_repayment_method.html" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Add Method</a>
</div>

<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-hand-holding-usd me-2"></i>All Repayment Methods</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Method Name</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($methods as $index => $method): ?>
                    <tr>
                        <td><?php echo $index + 1; ?></td>
                        <td><strong><?php echo $method['name']; ?></strong></td>
                        <td><span class="badge bg-success"><?php echo $method['status']; ?></span></td>
                        <td>
                            <a href="add_loan_repayment_method_edit.html?id=<?php echo $method['id']; ?>" class="btn btn-sm btn-outline-secondary"><i class="fas fa-edit"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include $base_path . 'includes/footer.php'; ?>
