<?php
$base_path = '../';
$page_title = 'E-Signature';
$current_page = 'e_signature';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$documents = [
    ['id' => 1, 'name' => 'Loan Agreement - John Doe', 'borrower' => 'John Doe', 'loan_id' => 'LN-2026-001', 'sent_date' => '2026-01-10', 'signed_date' => '2026-01-11', 'status' => 'signed'],
    ['id' => 2, 'name' => 'Loan Agreement - Jane Smith', 'borrower' => 'Jane Smith', 'loan_id' => 'LN-2026-002', 'sent_date' => '2026-01-12', 'signed_date' => null, 'status' => 'pending'],
    ['id' => 3, 'name' => 'Collateral Agreement - Michael Brown', 'borrower' => 'Michael Brown', 'loan_id' => 'LN-2026-003', 'sent_date' => '2026-01-15', 'signed_date' => '2026-01-16', 'status' => 'signed'],
    ['id' => 4, 'name' => 'Guarantor Form - Sarah Wilson', 'borrower' => 'Sarah Wilson', 'loan_id' => 'LN-2026-004', 'sent_date' => '2026-01-17', 'signed_date' => null, 'status' => 'expired'],
    ['id' => 5, 'name' => 'Loan Agreement - David Lee', 'borrower' => 'David Lee', 'loan_id' => 'LN-2026-005', 'sent_date' => '2026-01-18', 'signed_date' => null, 'status' => 'pending'],
];

include $base_path . 'includes/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><i class="fas fa-file-signature me-2"></i><?php echo __('e_signature'); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item active"><?php echo __('e_signature'); ?></li>
            </ol>
        </nav>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#sendDocumentModal">
        <i class="fas fa-paper-plane me-2"></i><?php echo __('send_document'); ?>
    </button>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="stat-icon primary">
                <i class="fas fa-file-alt"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo count($documents); ?></h3>
                <p><?php echo __('total_documents'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="stat-icon success">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo count(array_filter($documents, fn($d) => $d['status'] === 'signed')); ?></h3>
                <p><?php echo __('signed'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="stat-icon warning">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo count(array_filter($documents, fn($d) => $d['status'] === 'pending')); ?></h3>
                <p><?php echo __('pending'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="stat-icon danger">
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo count(array_filter($documents, fn($d) => $d['status'] === 'expired')); ?></h3>
                <p><?php echo __('expired'); ?></p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5><i class="fas fa-list me-2"></i><?php echo __('documents'); ?></h5>
        <div class="d-flex gap-2">
            <select class="form-select form-select-sm" style="width: 150px;">
                <option value=""><?php echo __('all_status'); ?></option>
                <option value="signed"><?php echo __('signed'); ?></option>
                <option value="pending"><?php echo __('pending'); ?></option>
                <option value="expired"><?php echo __('expired'); ?></option>
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
                        <th><?php echo __('document_name'); ?></th>
                        <th><?php echo __('borrower'); ?></th>
                        <th><?php echo __('loan_number'); ?></th>
                        <th><?php echo __('sent_date'); ?></th>
                        <th><?php echo __('signed_date'); ?></th>
                        <th><?php echo __('status'); ?></th>
                        <th><?php echo __('actions'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($documents as $doc): ?>
                    <tr>
                        <td><?php echo $doc['id']; ?></td>
                        <td><strong><i class="fas fa-file-pdf text-danger me-2"></i><?php echo htmlspecialchars($doc['name']); ?></strong></td>
                        <td><?php echo htmlspecialchars($doc['borrower']); ?></td>
                        <td><span class="badge bg-secondary"><?php echo $doc['loan_id']; ?></span></td>
                        <td><?php echo date('M d, Y', strtotime($doc['sent_date'])); ?></td>
                        <td><?php echo $doc['signed_date'] ? date('M d, Y', strtotime($doc['signed_date'])) : '-'; ?></td>
                        <td>
                            <?php
                            $status_class = $doc['status'] === 'signed' ? 'success' : ($doc['status'] === 'pending' ? 'warning' : 'danger');
                            ?>
                            <span class="badge bg-<?php echo $status_class; ?>"><?php echo __($doc['status']); ?></span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-info" title="<?php echo __('view'); ?>"><i class="fas fa-eye"></i></button>
                                <button class="btn btn-outline-primary" title="<?php echo __('download'); ?>"><i class="fas fa-download"></i></button>
                                <?php if ($doc['status'] === 'pending'): ?>
                                <button class="btn btn-outline-warning" title="<?php echo __('resend'); ?>"><i class="fas fa-redo"></i></button>
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

<div class="modal fade" id="sendDocumentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo __('send_document'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label"><?php echo __('borrower'); ?></label>
                            <select class="form-select" required>
                                <option value=""><?php echo __('select_borrower'); ?>...</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><?php echo __('loan'); ?></label>
                            <select class="form-select" required>
                                <option value=""><?php echo __('select_loan'); ?>...</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('document_template'); ?></label>
                        <select class="form-select" required>
                            <option value=""><?php echo __('select'); ?>...</option>
                            <option value="loan_agreement"><?php echo __('loan_agreement'); ?></option>
                            <option value="collateral_agreement"><?php echo __('collateral_agreement'); ?></option>
                            <option value="guarantor_form"><?php echo __('guarantor_form'); ?></option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('recipient_email'); ?></label>
                        <input type="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('message'); ?></label>
                        <textarea class="form-control" rows="3" placeholder="<?php echo __('optional_message'); ?>"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('expiry_days'); ?></label>
                        <select class="form-select">
                            <option value="7">7 <?php echo __('days'); ?></option>
                            <option value="14" selected>14 <?php echo __('days'); ?></option>
                            <option value="30">30 <?php echo __('days'); ?></option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo __('cancel'); ?></button>
                <button type="button" class="btn btn-primary"><i class="fas fa-paper-plane me-2"></i><?php echo __('send'); ?></button>
            </div>
        </div>
    </div>
</div>

<?php include $base_path . 'includes/footer.php'; ?>
