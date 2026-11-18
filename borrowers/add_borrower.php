<?php
$base_path = '../';
$page_title = 'Add Borrower';
$current_page = 'borrowers';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';
require_once $base_path . 'helpers/upload.php';

$error = '';
$success = '';
$file_errors = [];

// Edit mode: ?id=N loads the borrower into the same form.
$edit_id = (int)($_GET['id'] ?? 0);
$existing = null;
if ($edit_id) {
    $stmt = $con->prepare("SELECT * FROM borrower WHERE id = ?");
    $stmt->execute([$edit_id]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    if (!$existing) {
        $error = 'Borrower not found.';
    }
    $page_title = 'Edit Borrower';
}

// Form values: the submitted POST, else the stored borrower, else empty.
$form = $_POST;
if (empty($_POST) && $existing) {
    $form = [
        'borrower_firstname' => $existing['fname'], 'borrower_lastname' => $existing['lname'],
        'borrower_gender' => $existing['gender'], 'borrower_country' => $existing['country'],
        'borrower_title' => $existing['title'], 'borrower_mobile' => $existing['mobile_no'],
        'borrower_email' => $existing['email'], 'borrower_unique_number' => $existing['unique_no'],
        'borrower_dob' => $existing['dob'], 'borrower_address' => $existing['address'],
        'borrower_city' => $existing['city'], 'borrower_province' => $existing['province_state'],
        'borrower_zipcode' => $existing['zipcode'], 'borrower_landline' => $existing['landline'],
        'borrower_business_name' => $existing['business_name'], 'borrower_working_status' => $existing['working_status'],
        'borrower_description' => $existing['description'],
    ];
}

if (!empty($_POST) && isset($_POST['borrower_firstname']) && (!$edit_id || $existing)) {
    $fname = trim($_POST['borrower_firstname'] ?? '');
    $lname = trim($_POST['borrower_lastname'] ?? '');
    $gender = $_POST['borrower_gender'] ?? '';
    $country = $_POST['borrower_country'] ?? '';
    $title = $_POST['borrower_title'] ?? '';
    $mobile = trim($_POST['borrower_mobile'] ?? '');
    $email = trim($_POST['borrower_email'] ?? '');
    $unique_no = trim($_POST['borrower_unique_number'] ?? '');
    $dob = $_POST['borrower_dob'] ?? null;
    $address = trim($_POST['borrower_address'] ?? '');
    $city = trim($_POST['borrower_city'] ?? '');
    $province = trim($_POST['borrower_province'] ?? '');
    $zipcode = trim($_POST['borrower_zipcode'] ?? '');
    $landline = trim($_POST['borrower_landline'] ?? '');
    $business_name = trim($_POST['borrower_business_name'] ?? '');
    $working_status = $_POST['borrower_working_status'] ?? '';
    $description = trim($_POST['borrower_description'] ?? '');

    $photo_path = $existing['field2'] ?? null;
    if (isset($_FILES['user_picture']) && $_FILES['user_picture']['error'] !== UPLOAD_ERR_NO_FILE) {
        try {
            $photo_path = safe_upload($_FILES['user_picture'], 'uploads/borrowers', UPLOAD_IMAGE_TYPES, 5242880);
        } catch (RuntimeException $e) {
            $error = 'Photo: ' . $e->getMessage();
        }
    }

    if (!$fname || !$lname) {
        $error = __('please_fill_required_fields');
    } elseif ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif ($dob && !date_create($dob)) {
        $error = 'Please enter a valid date of birth.';
    }

    if (!$error) {
        $values = [
            $fname, $lname, $gender, $country, $title, $mobile, $email,
            $unique_no, $dob ?: null, $address, $city, $province, $zipcode,
            $landline, $business_name, $working_status, $description, $photo_path,
        ];
        try {
            $con->beginTransaction();
            if ($existing) {
                $stmt = $con->prepare("
                    UPDATE borrower SET fname = ?, lname = ?, gender = ?, country = ?, title = ?, mobile_no = ?, email = ?,
                        unique_no = ?, dob = ?, address = ?, city = ?, province_state = ?, zipcode = ?, landline = ?,
                        business_name = ?, working_status = ?, description = ?, field2 = ?
                    WHERE id = ?
                ");
                $stmt->execute(array_merge($values, [$edit_id]));
                $borrowerId = $edit_id;
            } else {
                $stmt = $con->prepare("
                    INSERT INTO borrower (fname, lname, gender, country, title, mobile_no, email, unique_no, dob, address, city, province_state, zipcode, landline, business_name, working_status, description, field2, staff_id, creation_date)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
                    RETURNING id
                ");
                $stmt->execute(array_merge($values, [$_SESSION['user_id'] ?? null]));
                $borrowerId = (int)$stmt->fetchColumn();
            }

            // Handle multiple file attachments
            if (isset($_FILES['borrower_files']) && !empty($_FILES['borrower_files']['name'][0])) {
                $stmt2 = $con->prepare("INSERT INTO borrower_files (borrower_id, name, filepath, creation_user) VALUES (?, ?, ?, ?)");
                foreach ($_FILES['borrower_files']['tmp_name'] as $k => $tmp) {
                    $one = [
                        'name' => $_FILES['borrower_files']['name'][$k],
                        'tmp_name' => $tmp,
                        'error' => $_FILES['borrower_files']['error'][$k],
                        'size' => $_FILES['borrower_files']['size'][$k],
                    ];
                    try {
                        $stored = safe_upload($one, 'uploads/borrower_docs', UPLOAD_DOCUMENT_TYPES, 10485760);
                        $stmt2->execute([$borrowerId, basename($one['name']), $stored, $_SESSION['user_id'] ?? 0]);
                    } catch (RuntimeException $e) {
                        $file_errors[] = basename($one['name']) . ': ' . $e->getMessage();
                    }
                }
            }
            $con->commit();

            if ($file_errors) {
                $_SESSION['flash_error'] = 'Borrower saved, but some files were rejected: ' . implode('; ', $file_errors);
            }
            header('Location: view_borrower.php?id=' . $borrowerId . '&success=1');
            exit;
        } catch (PDOException $e) {
            if ($con->inTransaction()) {
                $con->rollBack();
            }
            $error = 'Error saving borrower: ' . $e->getMessage();
        }
    }
}

include $base_path . 'includes/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><?php echo $existing ? 'Edit Borrower' : __('add_borrower'); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item"><a href="view_borrowers.php"><?php echo __('borrowers'); ?></a></li>
                <li class="breadcrumb-item active"><?php echo $existing ? 'Edit Borrower' : __('add_borrower'); ?></li>
            </ol>
        </nav>
    </div>
    <a href="view_borrowers.php" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i><?php echo __('back'); ?>
    </a>
</div>

<?php if ($error): ?>
<div class="alert alert-danger alert-dismissible fade show"><i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>

<form action="" method="POST" enctype="multipart/form-data">
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-user me-2"></i><?php echo __('personal_information'); ?></h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-2">
                            <label class="form-label"><?php echo __('title'); ?></label>
                            <select name="borrower_title" class="form-select">
                                <option value="">-</option>
                                <option value="Mr">Mr</option>
                                <option value="Mrs">Mrs</option>
                                <option value="Ms">Ms</option>
                                <option value="Dr">Dr</option>
                                <option value="Prof">Prof</option>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label"><?php echo __('first_name'); ?> <span class="text-danger">*</span></label>
                            <input type="text" name="borrower_firstname" class="form-control" placeholder="<?php echo __('first_name'); ?>" value="<?php echo htmlspecialchars($form['borrower_firstname'] ?? ''); ?>" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label"><?php echo __('last_name'); ?> <span class="text-danger">*</span></label>
                            <input type="text" name="borrower_lastname" class="form-control" placeholder="<?php echo __('last_name'); ?>" value="<?php echo htmlspecialchars($form['borrower_lastname'] ?? ''); ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label"><?php echo __('gender'); ?> <span class="text-danger">*</span></label>
                            <select name="borrower_gender" class="form-select" required>
                                <option value="">-- <?php echo __('select'); ?> --</option>
                                <option value="Male" <?php echo ($form['borrower_gender'] ?? '') === 'Male' ? 'selected' : ''; ?>>Male</option>
                                <option value="Female" <?php echo ($form['borrower_gender'] ?? '') === 'Female' ? 'selected' : ''; ?>>Female</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label"><?php echo __('date_of_birth'); ?></label>
                            <input type="date" name="borrower_dob" class="form-control" value="<?php echo htmlspecialchars($form['borrower_dob'] ?? ''); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label"><?php echo __('unique_id'); ?></label>
                            <input type="text" name="borrower_unique_number" class="form-control" placeholder="National ID / Passport" value="<?php echo htmlspecialchars($form['borrower_unique_number'] ?? ''); ?>">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <h5><i class="fas fa-address-book me-2"></i><?php echo __('contact_information'); ?></h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label"><?php echo __('mobile'); ?> <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">+255</span>
                                <input type="text" name="borrower_mobile" class="form-control" placeholder="7XXXXXXXX" value="<?php echo htmlspecialchars($form['borrower_mobile'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><?php echo __('landline'); ?></label>
                            <input type="text" name="borrower_landline" class="form-control" placeholder="Landline number" value="<?php echo htmlspecialchars($form['borrower_landline'] ?? ''); ?>">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label"><?php echo __('email'); ?></label>
                            <input type="email" name="borrower_email" class="form-control" placeholder="email@example.com" value="<?php echo htmlspecialchars($form['borrower_email'] ?? ''); ?>">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label"><?php echo __('address'); ?></label>
                            <input type="text" name="borrower_address" class="form-control" placeholder="Street address" value="<?php echo htmlspecialchars($form['borrower_address'] ?? ''); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label"><?php echo __('city'); ?></label>
                            <input type="text" name="borrower_city" class="form-control" placeholder="City" value="<?php echo htmlspecialchars($form['borrower_city'] ?? ''); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label"><?php echo __('province_state'); ?></label>
                            <input type="text" name="borrower_province" class="form-control" placeholder="Province/Region" value="<?php echo htmlspecialchars($form['borrower_province'] ?? ''); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label"><?php echo __('zip_code'); ?></label>
                            <input type="text" name="borrower_zipcode" class="form-control" placeholder="Postcode" value="<?php echo htmlspecialchars($form['borrower_zipcode'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><?php echo __('country'); ?></label>
                            <select name="borrower_country" class="form-select">
                                <option value="">-- <?php echo __('select_country'); ?> --</option>
                                <option value="TZ" <?php echo ($form['borrower_country'] ?? '') === 'TZ' ? 'selected' : ''; ?>>Tanzania</option>
                                <option value="KE" <?php echo ($form['borrower_country'] ?? '') === 'KE' ? 'selected' : ''; ?>>Kenya</option>
                                <option value="UG" <?php echo ($form['borrower_country'] ?? '') === 'UG' ? 'selected' : ''; ?>>Uganda</option>
                                <option value="RW" <?php echo ($form['borrower_country'] ?? '') === 'RW' ? 'selected' : ''; ?>>Rwanda</option>
                                <option value="BI" <?php echo ($form['borrower_country'] ?? '') === 'BI' ? 'selected' : ''; ?>>Burundi</option>
                                <option value="ZA" <?php echo ($form['borrower_country'] ?? '') === 'ZA' ? 'selected' : ''; ?>>South Africa</option>
                                <option value="NG" <?php echo ($form['borrower_country'] ?? '') === 'NG' ? 'selected' : ''; ?>>Nigeria</option>
                                <option value="GH" <?php echo ($form['borrower_country'] ?? '') === 'GH' ? 'selected' : ''; ?>>Ghana</option>
                                <option value="ZW" <?php echo ($form['borrower_country'] ?? '') === 'ZW' ? 'selected' : ''; ?>>Zimbabwe</option>
                                <option value="ZM" <?php echo ($form['borrower_country'] ?? '') === 'ZM' ? 'selected' : ''; ?>>Zambia</option>
                                <option value="MW" <?php echo ($form['borrower_country'] ?? '') === 'MW' ? 'selected' : ''; ?>>Malawi</option>
                                <option value="MZ" <?php echo ($form['borrower_country'] ?? '') === 'MZ' ? 'selected' : ''; ?>>Mozambique</option>
                                <option value="ET" <?php echo ($form['borrower_country'] ?? '') === 'ET' ? 'selected' : ''; ?>>Ethiopia</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <h5><i class="fas fa-briefcase me-2"></i><?php echo __('employment_information'); ?></h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label"><?php echo __('business_name'); ?></label>
                            <input type="text" name="borrower_business_name" class="form-control" placeholder="Company or Business name" value="<?php echo htmlspecialchars($form['borrower_business_name'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><?php echo __('working_status'); ?></label>
                            <select name="borrower_working_status" class="form-select">
                                <option value="">-- <?php echo __('select'); ?> --</option>
                                <option value="Employed" <?php echo ($form['borrower_working_status'] ?? '') === 'Employed' ? 'selected' : ''; ?>><?php echo __('employed'); ?></option>
                                <option value="Self-Employed" <?php echo ($form['borrower_working_status'] ?? '') === 'Self-Employed' ? 'selected' : ''; ?>><?php echo __('self_employed'); ?></option>
                                <option value="Unemployed" <?php echo ($form['borrower_working_status'] ?? '') === 'Unemployed' ? 'selected' : ''; ?>><?php echo __('unemployed'); ?></option>
                                <option value="Student" <?php echo ($form['borrower_working_status'] ?? '') === 'Student' ? 'selected' : ''; ?>><?php echo __('student'); ?></option>
                                <option value="Retired" <?php echo ($form['borrower_working_status'] ?? '') === 'Retired' ? 'selected' : ''; ?>><?php echo __('retired'); ?></option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label"><?php echo __('notes'); ?></label>
                            <textarea name="borrower_description" class="form-control" rows="3" placeholder="Additional notes..."><?php echo htmlspecialchars($form['borrower_description'] ?? ''); ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <h5><i class="fas fa-paperclip me-2"></i><?php echo __('attachments'); ?></h5>
                </div>
                <div class="card-body">
                    <label class="form-label"><?php echo __('borrower_files'); ?></label>
                    <input type="file" name="borrower_files[]" class="form-control" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                    <small class="text-muted"><?php echo __('upload_documents_hint'); ?></small>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-camera me-2"></i><?php echo __('photo'); ?></h5>
                </div>
                <div class="card-body text-center">
                    <div id="photoPreviewWrap" class="mb-3">
                        <img id="photoPreview" src="https://ui-avatars.com/api/?name=New+Borrower&size=150&background=2c7be5&color=fff" class="rounded-circle" style="width:120px;height:120px;object-fit:cover;">
                    </div>
                    <input type="file" name="user_picture" id="userPicture" class="form-control" accept="image/*" onchange="previewPhoto(this)">
                    <small class="text-muted d-block mt-2"><?php echo __('upload_photo_hint'); ?></small>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary w-100 mb-2">
                        <i class="fas fa-save me-2"></i><?php echo __('save_borrower'); ?>
                    </button>
                    <a href="view_borrowers.php" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-times me-2"></i><?php echo __('cancel'); ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
function previewPhoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('photoPreview').src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<script>
// Re-select dropdown values (title, gender, country, working status) when editing or after a failed submit.
(function () {
    const form = <?php echo json_encode(array_map(fn($v) => is_scalar($v) ? (string)$v : '', $form), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
    document.querySelectorAll('select[name^="borrower_"]').forEach(function (sel) {
        if (form[sel.name] !== undefined && form[sel.name] !== '') sel.value = form[sel.name];
    });
})();
</script>

<?php include $base_path . 'includes/footer.php'; ?>
