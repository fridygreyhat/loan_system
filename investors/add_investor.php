<?php
$base_path = '../';
$page_title = 'Add Investor';
$current_page = 'investors';

require_once $base_path . 'helpers/connect.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $id_number = trim($_POST['id_number'] ?? '');
    $initial_investment = floatval($_POST['initial_investment'] ?? 0);
    $interest_rate = floatval($_POST['interest_rate'] ?? 0);
    
    if ($first_name && $last_name) {
        try {
            $stmt = $con->prepare("INSERT INTO investors (first_name, last_name, email, phone, address, id_number, status) VALUES (?, ?, ?, ?, ?, ?, 'active') RETURNING id");
            $stmt->execute([$first_name, $last_name, $email, $phone, $address, $id_number]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $investor_id = $result['id'];
            
            if ($initial_investment > 0) {
                $account_number = 'INV' . date('Ymd') . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
                
                $stmt = $con->prepare("INSERT INTO investor_accounts (investor_id, account_number, investment_amount, interest_rate, status) VALUES (?, ?, ?, ?, 'active') RETURNING id");
                $stmt->execute([$investor_id, $account_number, $initial_investment, $interest_rate]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $account_id = $result['id'];
                
                $stmt = $con->prepare("INSERT INTO investor_transactions (account_id, type, amount, balance_after, description, created_by) VALUES (?, 'investment', ?, ?, 'Initial investment', 1)");
                $stmt->execute([$account_id, $initial_investment, $initial_investment]);
            }
            
            header("Location: view_investors.php?success=1");
            exit;
        } catch (PDOException $e) {
            $message = '<div class="alert alert-danger">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    } else {
        $message = '<div class="alert alert-warning">Please fill in required fields.</div>';
    }
}

include $base_path . 'includes/header.php';
?>

<div class="page-header">
    <h1>Add Investor</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="view_investors.php">Investors</a></li>
            <li class="breadcrumb-item active">Add Investor</li>
        </ol>
    </nav>
</div>

<?php echo $message; ?>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-user-plus me-2"></i>New Investor</h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <h6 class="text-uppercase text-muted mb-3">Personal Information</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Last Name <span class="text-danger">*</span></label>
                            <input type="text" name="last_name" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone</label>
                            <input type="tel" name="phone" class="form-control">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control" rows="2"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">ID Number</label>
                        <input type="text" name="id_number" class="form-control">
                    </div>
                    
                    <hr class="my-4">
                    
                    <h6 class="text-uppercase text-muted mb-3">Initial Investment (Optional)</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Investment Amount</label>
                            <div class="input-group">
                                <span class="input-group-text">TZS </span>
                                <input type="number" name="initial_investment" class="form-control" step="0.01" min="0" value="0">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Interest Rate (%)</label>
                            <div class="input-group">
                                <input type="number" name="interest_rate" class="form-control" step="0.01" min="0" value="0">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Add Investor
                        </button>
                        <a href="view_investors.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include $base_path . 'includes/footer.php'; ?>
