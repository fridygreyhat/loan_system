<?php
$base_path = '../';
$page_title = 'Payroll Templates';
$current_page = 'admin';

require_once $base_path . 'helpers/connect.php';

$templates = [
    ['id' => 1, 'name' => 'Standard Monthly', 'type' => 'Monthly', 'default' => true],
    ['id' => 2, 'name' => 'Bi-Weekly', 'type' => 'Bi-Weekly', 'default' => false],
    ['id' => 3, 'name' => 'Weekly', 'type' => 'Weekly', 'default' => false],
];

include $base_path . 'includes/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1>Payroll Templates</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="index.php">Admin</a></li>
                <li class="breadcrumb-item active">Payroll Templates</li>
            </ol>
        </nav>
    </div>
    <a href="edit_payroll_template.html" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Add Template</a>
</div>

<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-file-invoice-dollar me-2"></i>All Templates</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Template Name</th>
                        <th>Pay Period</th>
                        <th>Default</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($templates as $index => $template): ?>
                    <tr>
                        <td><?php echo $index + 1; ?></td>
                        <td><strong><?php echo $template['name']; ?></strong></td>
                        <td><?php echo $template['type']; ?></td>
                        <td>
                            <?php if ($template['default']): ?>
                            <span class="badge bg-success">Default</span>
                            <?php else: ?>
                            <a href="?set_default=<?php echo $template['id']; ?>" class="btn btn-sm btn-outline-secondary">Set Default</a>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="edit_payroll_template.html?id=<?php echo $template['id']; ?>" class="btn btn-sm btn-outline-secondary"><i class="fas fa-edit"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include $base_path . 'includes/footer.php'; ?>
