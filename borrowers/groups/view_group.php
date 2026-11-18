<?php
$base_path = '../../';
$page_title = 'Borrower Group';
$current_page = 'borrowers';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$group_id = (int)($_GET['id'] ?? 0);

$group = null;
$members = [];
$error = '';

if ($group_id > 0) {
    try {
        $stmt = $con->prepare("SELECT * FROM borrower_groups WHERE id = ?");
        $stmt->execute([$group_id]);
        $group = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;

        if ($group) {
            $stmt = $con->prepare("
                SELECT b.id, b.fname, b.lname, b.mobile_no, b.email, b.gender, b.city,
                       COUNT(l.id) AS loan_count,
                       COALESCE(SUM(l.principal_amount) FILTER (WHERE l.status = 'approved'), 0) AS active_principal
                FROM borrower b
                LEFT JOIN loans l ON l.borrower_id = b.id
                WHERE b.group_id = ?
                GROUP BY b.id
                ORDER BY b.fname, b.lname
            ");
            $stmt->execute([$group_id]);
            $members = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    } catch (PDOException $e) {
        $error = 'Could not load group: ' . $e->getMessage();
    }
}

include $base_path . 'includes/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><i class="fas fa-users text-primary me-2"></i><?php echo $group ? htmlspecialchars($group['group_name']) : 'Borrower Group'; ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>borrowers/view_borrowers.php"><?php echo __('borrowers'); ?></a></li>
                <li class="breadcrumb-item"><a href="view_groups.php"><?php echo __('borrower_groups'); ?></a></li>
                <li class="breadcrumb-item active"><?php echo __('view'); ?></li>
            </ol>
        </nav>
    </div>
    <?php if ($group): ?>
    <a href="add_borrowers_group.php?edit=<?php echo $group_id; ?>" class="btn btn-outline-secondary"><i class="fas fa-edit me-2"></i><?php echo __('edit'); ?></a>
    <?php endif; ?>
</div>

<?php if ($error): ?>
<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?></div>
<?php elseif (!$group): ?>
<div class="alert alert-warning"><i class="fas fa-exclamation-triangle me-2"></i>Group not found. <a href="view_groups.php">Back to groups</a></div>
<?php else: ?>
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Group Details</h5></div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Group Name</span><span><?php echo htmlspecialchars($group['group_name']); ?></span></li>
                <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Leader</span><span><?php echo htmlspecialchars($group['leader_name'] ?: '-'); ?></span></li>
                <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Members</span><span><?php echo count($members); ?></span></li>
                <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Created</span><span><?php echo $group['created_at'] ? date('d M Y', strtotime($group['created_at'])) : '-'; ?></span></li>
            </ul>
            <div class="card-body border-top">
                <small class="text-muted d-block mb-1"><?php echo __('description'); ?></small>
                <?php echo $group['description'] ? nl2br(htmlspecialchars($group['description'])) : '<span class="text-muted">-</span>'; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><h5 class="mb-0"><i class="fas fa-user-friends me-2"></i>Members (<?php echo count($members); ?>)</h5></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th><?php echo __('phone'); ?></th>
                                <th><?php echo __('email'); ?></th>
                                <th><?php echo __('city'); ?></th>
                                <th class="text-end"><?php echo __('loans'); ?></th>
                                <th class="text-end">Active Principal</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($members)): ?>
                            <tr><td colspan="7" class="text-center text-muted py-4">No borrowers are assigned to this group.</td></tr>
                            <?php endif; ?>
                            <?php foreach ($members as $m): ?>
                            <tr>
                                <td><a href="../view_borrower.php?id=<?php echo (int)$m['id']; ?>"><?php echo htmlspecialchars($m['fname'] . ' ' . $m['lname']); ?></a></td>
                                <td><?php echo htmlspecialchars($m['mobile_no'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($m['email'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($m['city'] ?? '-'); ?></td>
                                <td class="text-end"><?php echo (int)$m['loan_count']; ?></td>
                                <td class="text-end">TZS <?php echo number_format((float)$m['active_principal'], 2); ?></td>
                                <td><a href="../view_borrower.php?id=<?php echo (int)$m['id']; ?>" class="btn btn-sm btn-outline-primary" title="<?php echo __('view'); ?>"><i class="fas fa-eye"></i></a></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php include $base_path . 'includes/footer.php'; ?>
