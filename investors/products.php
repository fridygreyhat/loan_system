<?php
$base_path = '../';
$page_title = 'Investor Products';
$current_page = 'investors';
require_once '../helpers/connect.php';
require_once '../helpers/lang.php';
include '../includes/header.php';

$db = $con;

$products = [];
try {
    $stmt = $db->query("SELECT * FROM investor_products ORDER BY name");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $products = [];
    $load_error = 'Could not load products: ' . $e->getMessage();
}
?>

<?php if (!empty($_GET['error']) || !empty($load_error)): ?>
<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($_GET['error'] ?? $load_error); ?></div>
<?php elseif (isset($_GET['success'])): ?>
<div class="alert alert-success"><i class="fas fa-check-circle me-2"></i>Product saved.</div>
<?php endif; ?>

<div class="content-header">
    <h1><i class="fas fa-box"></i> <?php echo __('investor_products'); ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
            <li class="breadcrumb-item"><a href="view_investors.php"><?php echo __('investors'); ?></a></li>
            <li class="breadcrumb-item active"><?php echo __('investor_products'); ?></li>
        </ol>
    </nav>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><?php echo __('all_investor_products'); ?></h5>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
            <i class="fas fa-plus"></i> <?php echo __('add_investor_product'); ?>
        </button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th><?php echo __('product_name'); ?></th>
                        <th><?php echo __('interest_rate'); ?></th>
                        <th><?php echo __('interest_frequency'); ?></th>
                        <th><?php echo __('min_balance'); ?></th>
                        <th><?php echo __('status'); ?></th>
                        <th><?php echo __('actions'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($products)): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="fas fa-box-open fa-3x mb-3 d-block"></i>
                            <?php echo __('no_investor_products'); ?>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($products as $index => $product): ?>
                    <tr>
                        <td><?php echo $index + 1; ?></td>
                        <td><strong><?php echo htmlspecialchars($product['name'] ?? ''); ?></strong></td>
                        <td><?php echo number_format($product['interest_rate'] ?? 0, 2); ?>%</td>
                        <td><?php echo ucfirst($product['interest_frequency'] ?? 'monthly'); ?></td>
                        <td>TZS <?php echo number_format($product['min_balance'] ?? 0, 0); ?></td>
                        <td>
                            <span class="badge bg-<?php echo ($product['status'] ?? 'active') === 'active' ? 'success' : 'secondary'; ?>">
                                <?php echo ucfirst($product['status'] ?? 'active'); ?>
                            </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-outline-primary" title="<?php echo __('edit'); ?>"
                                        data-bs-toggle="modal" data-bs-target="#addProductModal"
                                        data-product="<?php echo htmlspecialchars(json_encode($product), ENT_QUOTES); ?>">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form method="POST" action="save_product.php" class="d-inline" onsubmit="return confirm('Delete this product?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo (int)$product['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="<?php echo __('delete'); ?>"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="addProductModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo __('add_investor_product'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="save_product.php" method="POST" id="productForm">
                <input type="hidden" name="id" value="">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('product_name'); ?> *</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?php echo __('interest_rate'); ?> (%) *</label>
                            <input type="number" name="interest_rate" class="form-control" step="0.01" value="10" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?php echo __('interest_frequency'); ?></label>
                            <select name="interest_frequency" class="form-select">
                                <option value="monthly"><?php echo __('monthly'); ?></option>
                                <option value="quarterly"><?php echo __('quarterly'); ?></option>
                                <option value="semi_annually"><?php echo __('semi_annually'); ?></option>
                                <option value="annually"><?php echo __('annually'); ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('min_balance'); ?></label>
                        <div class="input-group">
                            <span class="input-group-text">TZS</span>
                            <input type="number" name="min_balance" class="form-control" value="100000">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('description'); ?></label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('status'); ?></label>
                        <select name="status" class="form-select">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo __('cancel'); ?></button>
                    <button type="submit" class="btn btn-primary"><?php echo __('save_product'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// The same modal adds a product or, when opened from an Edit button, edits one.
document.getElementById('addProductModal').addEventListener('show.bs.modal', function (e) {
    const form = document.getElementById('productForm');
    const product = JSON.parse((e.relatedTarget && e.relatedTarget.getAttribute('data-product')) || '{}');
    form.reset();
    form.elements['id'].value = product.id || '';
    this.querySelector('.modal-title').textContent = product.id ? 'Edit Product' : <?php echo json_encode(__('add_investor_product')); ?>;
    ['name', 'interest_rate', 'interest_frequency', 'min_balance', 'description', 'status'].forEach(function (f) {
        if (form.elements[f] && product[f] !== undefined && product[f] !== null) form.elements[f].value = product[f];
    });
});
</script>

<?php include '../includes/footer.php'; ?>
