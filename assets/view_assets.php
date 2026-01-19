<?php
$base_path = '../';
$page_title = 'Asset Management';
$current_page = 'assets';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$assets = [
    ['id' => 1, 'name' => 'Office Computer - Dell OptiPlex', 'type' => 'Equipment', 'type_sw' => 'Vifaa', 'purchase_date' => '2024-01-15', 'purchase_price' => 2500000, 'current_value' => 2000000, 'depreciation' => 500000, 'status' => 'active'],
    ['id' => 2, 'name' => 'Office Furniture Set', 'type' => 'Furniture', 'type_sw' => 'Samani', 'purchase_date' => '2023-06-10', 'purchase_price' => 3500000, 'current_value' => 2800000, 'depreciation' => 700000, 'status' => 'active'],
    ['id' => 3, 'name' => 'Toyota Corolla - Office Vehicle', 'type' => 'Vehicle', 'type_sw' => 'Gari', 'purchase_date' => '2022-03-20', 'purchase_price' => 45000000, 'current_value' => 35000000, 'depreciation' => 10000000, 'status' => 'active'],
    ['id' => 4, 'name' => 'HP LaserJet Printer', 'type' => 'Equipment', 'type_sw' => 'Vifaa', 'purchase_date' => '2024-02-01', 'purchase_price' => 1200000, 'current_value' => 1000000, 'depreciation' => 200000, 'status' => 'active'],
    ['id' => 5, 'name' => 'Air Conditioning Unit', 'type' => 'Equipment', 'type_sw' => 'Vifaa', 'purchase_date' => '2023-11-15', 'purchase_price' => 1800000, 'current_value' => 1500000, 'depreciation' => 300000, 'status' => 'active'],
];

$total_purchase = array_sum(array_column($assets, 'purchase_price'));
$total_current = array_sum(array_column($assets, 'current_value'));
$total_depreciation = array_sum(array_column($assets, 'depreciation'));

$lang = get_current_lang();

include $base_path . 'includes/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><i class="fas fa-building me-2"></i><?php echo __('asset_management'); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item active"><?php echo __('asset_management'); ?></li>
            </ol>
        </nav>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAssetModal">
        <i class="fas fa-plus me-2"></i><?php echo __('add_asset'); ?>
    </button>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="stat-icon primary">
                <i class="fas fa-tags"></i>
            </div>
            <div class="stat-info">
                <h3>TZS <?php echo number_format($total_purchase); ?></h3>
                <p><?php echo __('total_purchase_value'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="stat-icon success">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="stat-info">
                <h3>TZS <?php echo number_format($total_current); ?></h3>
                <p><?php echo __('current_value'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="stat-icon warning">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-info">
                <h3>TZS <?php echo number_format($total_depreciation); ?></h3>
                <p><?php echo __('total_depreciation'); ?></p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5><i class="fas fa-list me-2"></i><?php echo __('asset_register'); ?></h5>
        <div class="d-flex gap-2">
            <select class="form-select form-select-sm" style="width: 150px;">
                <option value=""><?php echo __('all_types'); ?></option>
                <option value="Equipment"><?php echo __('equipment'); ?></option>
                <option value="Furniture"><?php echo __('furniture'); ?></option>
                <option value="Vehicle"><?php echo __('vehicle'); ?></option>
            </select>
            <input type="text" class="form-control form-control-sm" placeholder="<?php echo __('search'); ?>..." style="width: 200px;">
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th><?php echo __('asset_name'); ?></th>
                        <th><?php echo __('asset_type'); ?></th>
                        <th><?php echo __('purchase_date'); ?></th>
                        <th class="text-end"><?php echo __('purchase_price'); ?></th>
                        <th class="text-end"><?php echo __('current_value'); ?></th>
                        <th class="text-end"><?php echo __('depreciation'); ?></th>
                        <th><?php echo __('status'); ?></th>
                        <th><?php echo __('actions'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($assets as $asset): ?>
                    <tr>
                        <td><?php echo $asset['id']; ?></td>
                        <td><strong><?php echo htmlspecialchars($asset['name']); ?></strong></td>
                        <td>
                            <span class="badge bg-secondary"><?php echo $lang === 'sw' ? $asset['type_sw'] : $asset['type']; ?></span>
                        </td>
                        <td><?php echo date('M d, Y', strtotime($asset['purchase_date'])); ?></td>
                        <td class="text-end">TZS <?php echo number_format($asset['purchase_price']); ?></td>
                        <td class="text-end">TZS <?php echo number_format($asset['current_value']); ?></td>
                        <td class="text-end text-warning">TZS <?php echo number_format($asset['depreciation']); ?></td>
                        <td>
                            <span class="badge bg-success"><?php echo __($asset['status']); ?></span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary" title="<?php echo __('edit'); ?>"><i class="fas fa-edit"></i></button>
                                <button class="btn btn-outline-info" title="<?php echo __('view'); ?>"><i class="fas fa-eye"></i></button>
                                <button class="btn btn-outline-danger" title="<?php echo __('delete'); ?>"><i class="fas fa-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="addAssetModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo __('add_asset'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label"><?php echo __('asset_name'); ?></label>
                            <input type="text" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><?php echo __('asset_type'); ?></label>
                            <select class="form-select" required>
                                <option value=""><?php echo __('select'); ?>...</option>
                                <option value="Equipment"><?php echo __('equipment'); ?></option>
                                <option value="Furniture"><?php echo __('furniture'); ?></option>
                                <option value="Vehicle"><?php echo __('vehicle'); ?></option>
                                <option value="Property"><?php echo __('property'); ?></option>
                                <option value="Other"><?php echo __('other'); ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label"><?php echo __('purchase_date'); ?></label>
                            <input type="date" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><?php echo __('purchase_price'); ?> (TZS)</label>
                            <input type="number" class="form-control" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label"><?php echo __('depreciation_rate'); ?> (%)</label>
                            <input type="number" class="form-control" value="10">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><?php echo __('serial_number'); ?></label>
                            <input type="text" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('description'); ?></label>
                        <textarea class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('upload_files'); ?></label>
                        <input type="file" class="form-control" multiple>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo __('cancel'); ?></button>
                <button type="button" class="btn btn-primary"><?php echo __('save'); ?></button>
            </div>
        </div>
    </div>
</div>

<?php include $base_path . 'includes/footer.php'; ?>
