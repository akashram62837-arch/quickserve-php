<?php
require_once __DIR__ . '/../includes/functions.php';

if (is_provider_logged_in()) {
    redirect('provider/dashboard.php');
}

$categories = $pdo->query("SELECT * FROM categories WHERE status = 1 ORDER BY category_name ASC")->fetchAll();
$experienceOptions = [
    'Less than 1 year' => 0,
    '1-2 years'         => 2,
    '3-5 years'         => 5,
    '5-10 years'        => 10,
    '10+ years'         => 12,
];

$errors = [];
$old = ['fullName' => '', 'email' => '', 'phone' => '', 'category_id' => '', 'experience' => '', 'aadhar' => '', 'address' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $fullName   = trim($_POST['fullName'] ?? '');
    $email      = strtolower(trim($_POST['email'] ?? ''));
    $phone      = trim($_POST['phone'] ?? '');
    $password   = $_POST['password'] ?? '';
    $categoryId = (int) ($_POST['category_id'] ?? 0);
    $experience = $_POST['experience'] ?? '';
    $aadhar     = trim($_POST['aadhar'] ?? '');
    $address    = trim($_POST['address'] ?? '');
    $agree      = isset($_POST['agree']);

    $old = compact('fullName', 'email', 'phone', 'categoryId', 'experience', 'aadhar', 'address');
    $old['category_id'] = $categoryId;

    if (!$fullName || !$email || !$phone || !$password || !$categoryId || !$experience || !$aadhar || !$address) {
        $errors['general'] = 'Please fill in all required fields.';
    } elseif (!preg_match('/^\d{10}$/', $phone)) {
        $errors['phone'] = 'Please enter a valid 10-digit phone number.';
    } elseif (!preg_match('/^\d{12}$/', $aadhar)) {
        $errors['aadhar'] = 'Please enter a valid 12-digit Aadhar number.';
    } elseif (strlen($password) < 6) {
        $errors['password'] = 'Password must be at least 6 characters.';
    } elseif (!$agree) {
        $errors['general'] = 'Please accept the Terms & Conditions.';
    } elseif (!isset($experienceOptions[$experience])) {
        $errors['experience'] = 'Please select a valid experience range.';
    }

    if (!$errors) {
        $stmt = $pdo->prepare("SELECT id FROM providers WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors['general'] = 'A provider account with this email already exists.';
        }
    }

    if (!$errors) {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $expYears = $experienceOptions[$experience];
        $ins = $pdo->prepare(
            "INSERT INTO providers (full_name, email, password, phone, category_id, experience, address, profile_image, aadhaar_number, is_approved, status, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, '', ?, 0, 1, NOW(), NOW())"
        );
        $ins->execute([$fullName, $email, $hash, $phone, $categoryId, $expYears, $address, $aadhar]);

        set_flash('success', 'Registration submitted! Your account is pending admin approval. You will be able to login once approved.');
        redirect('provider/login.php');
    }
}

$pageTitle = 'Provider Registration';
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo e($pageTitle); ?> | QuickServe</title>
<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
</head>
<body>
<div class="auth-page">
  <div class="auth-card wide">
    <a href="<?php echo BASE_URL; ?>index.php" class="auth-back-link">← Back to Home</a>
    <div class="auth-card-top">
      <div class="logo-box" style="margin:0 auto 12px;">Q</div>
      <h1>Become a QuickServe Provider</h1>
      <p>Register to start receiving booking requests from customers.</p>
    </div>

    <?php render_flash(); ?>
    <?php if (!empty($errors['general'])): ?><div class="alert alert-error"><?php echo e($errors['general']); ?></div><?php endif; ?>

    <form method="POST" action="">
      <?php echo csrf_field(); ?>

      <div class="form-row">
        <div class="form-group">
          <label>Full Name *</label>
          <input type="text" name="fullName" placeholder="Enter full name" value="<?php echo e($old['fullName']); ?>" required>
        </div>
        <div class="form-group">
          <label>Email Address *</label>
          <input type="email" name="email" placeholder="Enter email" value="<?php echo e($old['email']); ?>" required>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label>Phone Number *</label>
          <input type="tel" name="phone" placeholder="10-digit mobile number" maxlength="10" value="<?php echo e($old['phone']); ?>" required>
          <?php if (!empty($errors['phone'])): ?><span class="form-error"><?php echo e($errors['phone']); ?></span><?php endif; ?>
        </div>
        <div class="form-group">
          <label>Password *</label>
          <input type="password" name="password" placeholder="Minimum 6 characters" minlength="6" required>
          <?php if (!empty($errors['password'])): ?><span class="form-error"><?php echo e($errors['password']); ?></span><?php endif; ?>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label>Service Category *</label>
          <select name="category_id" required>
            <option value="">Select service</option>
            <?php foreach ($categories as $cat): ?>
              <option value="<?php echo (int) $cat['id']; ?>" <?php echo (int) $old['category_id'] === (int) $cat['id'] ? 'selected' : ''; ?>><?php echo e($cat['category_name']); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label>Experience *</label>
          <select name="experience" required>
            <option value="">Select experience</option>
            <?php foreach (array_keys($experienceOptions) as $exp): ?>
              <option value="<?php echo e($exp); ?>" <?php echo $old['experience'] === $exp ? 'selected' : ''; ?>><?php echo e($exp); ?></option>
            <?php endforeach; ?>
          </select>
          <?php if (!empty($errors['experience'])): ?><span class="form-error"><?php echo e($errors['experience']); ?></span><?php endif; ?>
        </div>
      </div>

      <div class="form-group">
        <label>Aadhar Card Number *</label>
        <input type="text" name="aadhar" placeholder="12-digit Aadhar number" maxlength="12" value="<?php echo e($old['aadhar']); ?>" required>
        <?php if (!empty($errors['aadhar'])): ?><span class="form-error"><?php echo e($errors['aadhar']); ?></span><?php endif; ?>
      </div>

      <div class="form-group">
        <label>Address *</label>
        <textarea name="address" rows="2" placeholder="Enter your complete address" required><?php echo e($old['address']); ?></textarea>
      </div>

      <div class="checkbox-row">
        <input type="checkbox" name="agree" id="agree" required>
        <label for="agree">I agree to the Terms &amp; Conditions and Privacy Policy of QuickServe.</label>
      </div>

      <button type="submit" class="btn btn-primary btn-block">Submit Registration</button>
    </form>

    <p class="auth-footer-note">Already registered? <a href="<?php echo BASE_URL; ?>provider/login.php">Login here</a></p>
  </div>
</div>
<script src="<?php echo BASE_URL; ?>assets/js/main.js"></script>
</body>
</html>
