<?php
$base_path = '../../';
$page_title = 'Add Borrower Group';
$current_page = 'borrowers';

require_once $base_path . 'helpers/connect.php';

$group = null;
$edit_id = $_GET['edit'] ?? null;

if ($edit_id) {
    try {
        $stmt = $con->prepare("SELECT * FROM borrower_groups WHERE id = ?");
        $stmt->execute([$edit_id]);
        $group = $stmt->fetch(PDO::FETCH_ASSOC);
        $page_title = 'Edit Borrower Group';
    } catch (PDOException $e) {
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $group_name = $_POST['group_name'] ?? '';
    $leader_name = $_POST['leader_name'] ?? '';
    $description = $_POST['description'] ?? '';
    
    try {
        if ($edit_id && $group) {
            $stmt = $con->prepare("UPDATE borrower_groups SET group_name = ?, leader_name = ?, description = ? WHERE id = ?");
            $stmt->execute([$group_name, $leader_name, $description, $edit_id]);
        } else {
            $stmt = $con->prepare("INSERT INTO borrower_groups (group_name, leader_name, description) VALUES (?, ?, ?)");
            $stmt->execute([$group_name, $leader_name, $description]);
        }
        header('Location: view_borrowers_groups_branch.php');
        exit;
    } catch (PDOException $e) {
        $error = "Error saving group.";
    }
}

include $base_path . 'includes/header.php';
?>

<div class="page-header">
    <h1><?php echo $edit_id ? 'Edit' : 'Add'; ?> Borrower Group</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="view_borrowers_groups_branch.php">Groups</a></li>
            <li class="breadcrumb-item active"><?php echo $edit_id ? 'Edit' : 'Add'; ?> Group</li>
        </ol>
    </nav>
</div>

<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-users me-2"></i>Group Details</h5>
    </div>
    <div class="card-body">
        <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Group Name *</label>
                    <input type="text" name="group_name" class="form-control" required value="<?php echo htmlspecialchars($group['group_name'] ?? ''); ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Group Leader</label>
                    <input type="text" name="leader_name" class="form-control" value="<?php echo htmlspecialchars($group['leader_name'] ?? ''); ?>">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars($group['description'] ?? ''); ?></textarea>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Save Group</button>
                <a href="view_borrowers_groups_branch.php" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php include $base_path . 'includes/footer.php'; ?>
