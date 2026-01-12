<?php
$base_path = '../';
$page_title = 'Agreement Templates';
$current_page = 'settings';

require_once $base_path . 'helpers/connect.php';

$templates = [];
try {
    $stmt = $con->query("SELECT * FROM agreement_templates ORDER BY name");
    $templates = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
}

include $base_path . 'includes/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1>Agreement Templates</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="index.php">Settings</a></li>
                <li class="breadcrumb-item active">Agreement Templates</li>
            </ol>
        </nav>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadTemplateModal">
        <i class="fas fa-upload me-2"></i>Upload Template
    </button>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-file-word me-2"></i>Loan Agreement Templates</h5>
            </div>
            <div class="card-body">
                <?php if (empty($templates)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-file-alt fa-4x text-muted mb-3"></i>
                    <h5>No Templates Yet</h5>
                    <p class="text-muted">Upload your first DOCX template to auto-fill loan agreements.</p>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadTemplateModal">
                        <i class="fas fa-upload me-2"></i>Upload Template
                    </button>
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Template Name</th>
                                <th>Description</th>
                                <th>Placeholders</th>
                                <th>Default</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($templates as $template): ?>
                            <tr>
                                <td>
                                    <i class="fas fa-file-word text-primary me-2"></i>
                                    <strong><?php echo htmlspecialchars($template['name']); ?></strong>
                                </td>
                                <td><?php echo htmlspecialchars($template['description'] ?? '-'); ?></td>
                                <td><small class="text-muted"><?php echo htmlspecialchars($template['placeholders'] ?? 'None'); ?></small></td>
                                <td>
                                    <?php if ($template['is_default']): ?>
                                    <span class="badge bg-success">Default</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="download_template.php?id=<?php echo $template['id']; ?>" class="btn btn-outline-primary" title="Download">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        <button class="btn btn-outline-secondary" title="Preview">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-danger" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-info-circle me-2"></i>How It Works</h5>
            </div>
            <div class="card-body">
                <ol class="ps-3">
                    <li class="mb-2">Create a DOCX file with your loan agreement</li>
                    <li class="mb-2">Add placeholders like <code>{borrower_name}</code></li>
                    <li class="mb-2">Upload the template here</li>
                    <li class="mb-2">When creating loans, the system auto-fills the template</li>
                    <li>Download filled agreements as DOCX or PDF</li>
                </ol>
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-header">
                <h5><i class="fas fa-code me-2"></i>Available Placeholders</h5>
            </div>
            <div class="card-body">
                <h6 class="text-uppercase text-muted small">Borrower Info</h6>
                <code class="d-block mb-1">{borrower_name}</code>
                <code class="d-block mb-1">{borrower_address}</code>
                <code class="d-block mb-1">{borrower_phone}</code>
                <code class="d-block mb-1">{borrower_email}</code>
                <code class="d-block mb-3">{borrower_id}</code>
                
                <h6 class="text-uppercase text-muted small">Loan Info</h6>
                <code class="d-block mb-1">{loan_amount}</code>
                <code class="d-block mb-1">{interest_rate}</code>
                <code class="d-block mb-1">{loan_term}</code>
                <code class="d-block mb-1">{monthly_payment}</code>
                <code class="d-block mb-1">{total_repayment}</code>
                <code class="d-block mb-1">{disbursement_date}</code>
                <code class="d-block mb-3">{maturity_date}</code>
                
                <h6 class="text-uppercase text-muted small">Other</h6>
                <code class="d-block mb-1">{current_date}</code>
                <code class="d-block mb-1">{company_name}</code>
                <code class="d-block">{branch_name}</code>
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-header">
                <h5><i class="fas fa-download me-2"></i>Sample Template</h5>
            </div>
            <div class="card-body">
                <p class="text-muted">Download our sample template to get started quickly.</p>
                <a href="sample_agreement.docx" class="btn btn-outline-primary">
                    <i class="fas fa-file-word me-2"></i>Download Sample
                </a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="uploadTemplateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Agreement Template</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="upload_template.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Template Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g., Standard Loan Agreement" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="Brief description of this template"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Template File (DOCX)</label>
                        <input type="file" name="template_file" class="form-control" accept=".docx" required>
                        <small class="text-muted">Only .docx files are accepted</small>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="is_default" name="is_default">
                        <label class="form-check-label" for="is_default">Set as default template</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload me-2"></i>Upload Template
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include $base_path . 'includes/footer.php'; ?>
