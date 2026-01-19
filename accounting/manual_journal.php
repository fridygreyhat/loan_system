<?php
$base_path = '../';
$page_title = 'Manual Journal';
$current_page = 'accounting';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$journals = [];
try {
    $db = $con;
    $stmt = $db->query("SELECT * FROM journal_entries ORDER BY date DESC LIMIT 50");
    $journals = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $journals = [];
}

include $base_path . 'includes/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><?php echo __('manual_journal'); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item"><a href="#"><?php echo __('accounting'); ?></a></li>
                <li class="breadcrumb-item active"><?php echo __('manual_journal'); ?></li>
            </ol>
        </nav>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addJournalModal">
        <i class="fas fa-plus me-2"></i><?php echo __('add_entry'); ?>
    </button>
</div>

<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-file-alt me-2"></i><?php echo __('journal_entry'); ?>s</h5>
        <input type="text" class="form-control form-control-sm" placeholder="<?php echo __('search'); ?>..." style="width: 200px;">
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th><?php echo __('reference'); ?></th>
                        <th><?php echo __('journal_date'); ?></th>
                        <th><?php echo __('description'); ?></th>
                        <th class="text-end"><?php echo __('debit'); ?></th>
                        <th class="text-end"><?php echo __('credit'); ?></th>
                        <th><?php echo __('status'); ?></th>
                        <th><?php echo __('actions'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($journals as $journal): ?>
                    <tr>
                        <td><span class="badge bg-secondary"><?php echo $journal['id']; ?></span></td>
                        <td><?php echo date('M d, Y', strtotime($journal['date'])); ?></td>
                        <td><?php echo htmlspecialchars($journal['description']); ?></td>
                        <td class="text-end">TZS <?php echo number_format($journal['debit'], 2); ?></td>
                        <td class="text-end">TZS <?php echo number_format($journal['credit'], 2); ?></td>
                        <td>
                            <span class="badge bg-<?php echo $journal['status'] === 'posted' ? 'success' : 'warning'; ?>">
                                <?php echo __($journal['status']); ?>
                            </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary" title="<?php echo __('view'); ?>"><i class="fas fa-eye"></i></button>
                                <?php if ($journal['status'] !== 'posted'): ?>
                                <button class="btn btn-outline-success" title="Post"><i class="fas fa-check"></i></button>
                                <button class="btn btn-outline-secondary" title="<?php echo __('edit'); ?>"><i class="fas fa-edit"></i></button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="addJournalModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo __('add_entry'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label"><?php echo __('journal_date'); ?></label>
                            <input type="date" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><?php echo __('reference'); ?></label>
                            <input type="text" class="form-control" placeholder="Auto-generated">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('description'); ?></label>
                        <textarea class="form-control" rows="2"></textarea>
                    </div>
                    <hr>
                    <h6><?php echo __('journal_entry'); ?> Lines</h6>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th><?php echo __('account_name'); ?></th>
                                <th><?php echo __('debit'); ?></th>
                                <th><?php echo __('credit'); ?></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <select class="form-select form-select-sm">
                                        <option>Cash & Bank</option>
                                        <option>Loans Receivable</option>
                                        <option>Interest Income</option>
                                    </select>
                                </td>
                                <td><input type="number" class="form-control form-control-sm" placeholder="0.00"></td>
                                <td><input type="number" class="form-control form-control-sm" placeholder="0.00"></td>
                                <td><button type="button" class="btn btn-sm btn-outline-danger"><i class="fas fa-times"></i></button></td>
                            </tr>
                            <tr>
                                <td>
                                    <select class="form-select form-select-sm">
                                        <option>Cash & Bank</option>
                                        <option>Loans Receivable</option>
                                        <option>Interest Income</option>
                                    </select>
                                </td>
                                <td><input type="number" class="form-control form-control-sm" placeholder="0.00"></td>
                                <td><input type="number" class="form-control form-control-sm" placeholder="0.00"></td>
                                <td><button type="button" class="btn btn-sm btn-outline-danger"><i class="fas fa-times"></i></button></td>
                            </tr>
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-sm btn-outline-primary"><i class="fas fa-plus me-2"></i>Add Line</button>
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
