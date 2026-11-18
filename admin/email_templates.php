<?php
$base_path = '../';
$page_title = 'Email Templates';
$current_page = 'settings';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$error = '';
$form = [
    'id' => 0,
    'name' => '',
    'type' => '',
    'subject' => '',
    'body' => '',
    'placeholders' => '',
    'is_active' => true,
];

$flash_messages = [
    'added'   => 'Email template added.',
    'updated' => 'Email template updated.',
    'deleted' => 'Email template deleted.',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = (int)($_POST['id'] ?? 0);

    if ($action === 'delete') {
        if ($id <= 0) {
            $error = 'Invalid template.';
        } else {
            try {
                $stmt = $con->prepare("DELETE FROM email_templates WHERE id = ?");
                $stmt->execute([$id]);
                if ($stmt->rowCount() === 0) {
                    $error = 'Email template not found.';
                } else {
                    header('Location: email_templates.php?msg=deleted');
                    exit;
                }
            } catch (PDOException $e) {
                error_log('email_templates delete: ' . $e->getMessage());
                $error = 'Could not delete the template: database error.';
            }
        }
    } elseif ($action === 'add' || $action === 'update') {
        $form = [
            'id' => $id,
            'name' => trim($_POST['name'] ?? ''),
            'type' => trim($_POST['type'] ?? ''),
            'subject' => trim($_POST['subject'] ?? ''),
            'body' => trim($_POST['body'] ?? ''),
            'placeholders' => trim($_POST['placeholders'] ?? ''),
            'is_active' => isset($_POST['is_active']),
        ];

        if ($form['name'] === '' || $form['type'] === '' || $form['subject'] === '' || $form['body'] === '') {
            $error = 'Name, type, subject and body are required.';
        } elseif (mb_strlen($form['name']) > 100) {
            $error = 'Name is too long (max 100 characters).';
        } elseif (mb_strlen($form['type']) > 50) {
            $error = 'Type is too long (max 50 characters).';
        } elseif (mb_strlen($form['subject']) > 255) {
            $error = 'Subject is too long (max 255 characters).';
        } elseif ($action === 'update' && $id <= 0) {
            $error = 'Invalid template.';
        } else {
            // Auto-detect {placeholders} when none were entered.
            if ($form['placeholders'] === '') {
                preg_match_all('/\{([a-z0-9_]+)\}/i', $form['subject'] . ' ' . $form['body'], $m);
                $form['placeholders'] = implode(',', array_unique($m[1]));
            }
            try {
                if ($action === 'add') {
                    $stmt = $con->prepare("INSERT INTO email_templates (name, type, subject, body, placeholders, is_active) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->bindValue(1, $form['name']);
                    $stmt->bindValue(2, $form['type']);
                    $stmt->bindValue(3, $form['subject']);
                    $stmt->bindValue(4, $form['body']);
                    $stmt->bindValue(5, $form['placeholders'] !== '' ? $form['placeholders'] : null);
                    $stmt->bindValue(6, $form['is_active'], PDO::PARAM_BOOL);
                    $stmt->execute();
                    header('Location: email_templates.php?msg=added');
                    exit;
                }
                $stmt = $con->prepare("UPDATE email_templates SET name = ?, type = ?, subject = ?, body = ?, placeholders = ?, is_active = ? WHERE id = ?");
                $stmt->bindValue(1, $form['name']);
                $stmt->bindValue(2, $form['type']);
                $stmt->bindValue(3, $form['subject']);
                $stmt->bindValue(4, $form['body']);
                $stmt->bindValue(5, $form['placeholders'] !== '' ? $form['placeholders'] : null);
                $stmt->bindValue(6, $form['is_active'], PDO::PARAM_BOOL);
                $stmt->bindValue(7, $id, PDO::PARAM_INT);
                $stmt->execute();
                if ($stmt->rowCount() === 0) {
                    $error = 'Email template not found.';
                } else {
                    header('Location: email_templates.php?msg=updated');
                    exit;
                }
            } catch (PDOException $e) {
                error_log('email_templates save: ' . $e->getMessage());
                $error = 'Could not save the template: database error.';
            }
        }
    } else {
        $error = 'Unknown action.';
    }
}

// Load a template for editing (GET ?edit=N) unless a failed POST already filled the form.
$edit_not_found = false;
if ($_SERVER['REQUEST_METHOD'] !== 'POST' && isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    try {
        $stmt = $con->prepare("SELECT * FROM email_templates WHERE id = ?");
        $stmt->execute([$edit_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $form = [
                'id' => (int)$row['id'],
                'name' => $row['name'],
                'type' => $row['type'],
                'subject' => $row['subject'],
                'body' => $row['body'],
                'placeholders' => $row['placeholders'] ?? '',
                'is_active' => (bool)$row['is_active'],
            ];
        } else {
            $edit_not_found = true;
        }
    } catch (PDOException $e) {
        error_log('email_templates load: ' . $e->getMessage());
        $error = 'Could not load the template: database error.';
    }
}

$templates = [];
try {
    $templates = $con->query("SELECT * FROM email_templates ORDER BY name, id")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log('email_templates list: ' . $e->getMessage());
    $error = $error ?: 'Could not load email templates: database error.';
}

$is_edit = $form['id'] > 0;
$message = $flash_messages[$_GET['msg'] ?? ''] ?? '';

include $base_path . 'includes/header.php';
?>

<div class="content-header">
    <h1><i class="fas fa-envelope"></i> Email Templates</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
            <li class="breadcrumb-item"><a href="email_settings.php"><?php echo __('email_settings'); ?></a></li>
            <li class="breadcrumb-item active">Email Templates</li>
        </ol>
    </nav>
</div>

<?php if ($message): ?>
<div class="alert alert-success alert-dismissible fade show">
    <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($message); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if ($error): ?>
<div class="alert alert-danger alert-dismissible fade show">
    <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if ($edit_not_found): ?>
<div class="alert alert-warning">
    <i class="fas fa-exclamation-triangle me-2"></i>Email template not found.
</div>
<?php endif; ?>

<div class="row">
    <div class="col-lg-7 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-list me-2"></i>All Email Templates</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Subject</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($templates)): ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                    No email templates yet. Use the form to add one.
                                </td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($templates as $t): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($t['name']); ?></strong></td>
                                <td><span class="badge bg-light text-dark"><?php echo htmlspecialchars($t['type']); ?></span></td>
                                <td><?php echo htmlspecialchars($t['subject']); ?></td>
                                <td>
                                    <?php if ($t['is_active']): ?>
                                    <span class="badge bg-success">Active</span>
                                    <?php else: ?>
                                    <span class="badge bg-secondary">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="email_templates.php?edit=<?php echo (int)$t['id']; ?>" class="btn btn-outline-secondary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" class="d-inline" onsubmit="return confirm('Delete this email template?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo (int)$t['id']; ?>">
                                            <button type="submit" class="btn btn-outline-danger btn-sm" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
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
    </div>

    <div class="col-lg-5 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-<?php echo $is_edit ? 'edit' : 'plus'; ?> me-2"></i><?php echo $is_edit ? 'Edit Template' : 'Add Template'; ?>
                </h5>
                <?php if ($is_edit): ?>
                <a href="email_templates.php" class="btn btn-sm btn-outline-secondary">New</a>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <form method="POST" action="email_templates.php">
                    <input type="hidden" name="action" value="<?php echo $is_edit ? 'update' : 'add'; ?>">
                    <input type="hidden" name="id" value="<?php echo (int)$form['id']; ?>">

                    <div class="mb-3">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" maxlength="100" required value="<?php echo htmlspecialchars($form['name']); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Type <span class="text-danger">*</span></label>
                        <input type="text" name="type" class="form-control" maxlength="50" list="emailTemplateTypes" required value="<?php echo htmlspecialchars($form['type']); ?>" placeholder="e.g. reminder">
                        <datalist id="emailTemplateTypes">
                            <option value="reminder">
                            <option value="confirmation">
                            <option value="overdue">
                            <option value="approval">
                            <option value="statement">
                            <option value="birthday">
                            <option value="general">
                        </datalist>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Subject <span class="text-danger">*</span></label>
                        <input type="text" name="subject" class="form-control" maxlength="255" required value="<?php echo htmlspecialchars($form['subject']); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Body <span class="text-danger">*</span></label>
                        <textarea name="body" class="form-control" rows="8" required><?php echo htmlspecialchars($form['body']); ?></textarea>
                        <small class="text-muted">Use placeholders such as {borrower_name}, {amount}, {due_date}, {balance}.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Placeholders</label>
                        <input type="text" name="placeholders" class="form-control" value="<?php echo htmlspecialchars($form['placeholders']); ?>" placeholder="borrower_name,amount,due_date">
                        <small class="text-muted">Comma-separated. Left blank, they are detected from the subject and body.</small>
                    </div>
                    <div class="form-check mb-3">
                        <input type="checkbox" class="form-check-input" id="tplActive" name="is_active" value="1" <?php echo $form['is_active'] ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="tplActive">Active</label>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i><?php echo $is_edit ? 'Update Template' : 'Add Template'; ?>
                        </button>
                        <?php if ($is_edit): ?>
                        <a href="email_templates.php" class="btn btn-outline-secondary">Cancel</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include $base_path . 'includes/footer.php'; ?>
