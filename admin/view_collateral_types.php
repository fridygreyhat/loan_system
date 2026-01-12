<?php
$base_path = '../';
$page_title = 'Collateral Types';
$current_page = 'admin';

require_once $base_path . 'helpers/connect.php';

$types = [
    ['id' => 1, 'name' => 'Vehicle', 'description' => 'Cars, trucks, motorcycles', 'status' => 'Active'],
    ['id' => 2, 'name' => 'Property', 'description' => 'Land, buildings, houses', 'status' => 'Active'],
    ['id' => 3, 'name' => 'Equipment', 'description' => 'Machinery, tools', 'status' => 'Active'],
    ['id' => 4, 'name' => 'Jewelry', 'description' => 'Gold, silver, precious stones', 'status' => 'Active'],
    ['id' => 5, 'name' => 'Electronics', 'description' => 'Phones, laptops, TVs', 'status' => 'Active'],
];

include $base_path . 'includes/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1>Collateral Types</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="index.php">Admin</a></li>
                <li class="breadcrumb-item active">Collateral Types</li>
            </ol>
        </nav>
    </div>
    <a href="add_collateral_type.html" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Add Type</a>
</div>

<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-shield-alt me-2"></i>All Collateral Types</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Type Name</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($types as $index => $type): ?>
                    <tr>
                        <td><?php echo $index + 1; ?></td>
                        <td><strong><?php echo $type['name']; ?></strong></td>
                        <td><?php echo $type['description']; ?></td>
                        <td><span class="badge bg-success"><?php echo $type['status']; ?></span></td>
                        <td>
                            <a href="add_collateral_type_edit.html?id=<?php echo $type['id']; ?>" class="btn btn-sm btn-outline-secondary"><i class="fas fa-edit"></i></a>
                            <a href="?delete=<?php echo $type['id']; ?>" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include $base_path . 'includes/footer.php'; ?>
