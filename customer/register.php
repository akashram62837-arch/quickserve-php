<?php
require_once __DIR__ . '/../includes/functions.php';

if (is_customer_logged_in()) {
    redirect('index.php');
}

$errors = [];
$old = ['fullName' => '', 'email' => '', 'phone' => '', 'address' => ''];
$allowedDomains = ['gmail.com','yahoo.com','yahoo.in','outlook.com','hotmail.com','live.com','icloud.com','protonmail.com','proton.me','rediffmail.com','zoho.com','mail.com'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $fullName = trim($_POST['fullName'] ?? '');
    $email    = strtolower(trim($_POST['email'] ?? ''));
    $phoneRaw = trim($_POST['phone'] ?? '');
    $address  = trim($_POST['address'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirmPassword'] ?? '';

    $old = compact('fullName', 'email', 'phoneRaw', 'address');
    $old['phone'] = $phoneRaw;

    if (mb_strlen($fullName) < 2) {
        $errors['fullName'] = 'Please enter a valid full name.';
    }

    if ($email === '') {
        $errors['email'] = 'Email address is required.';
    } else {
        $domain = substr(strrchr($email, '@'), 1);
        $valid = preg_match('/^[a-z0-9][a-z0-9._%+-]*@[a-z0-9-]+\.[a-z]{2,}$/', $email)
            && strpos($email, '..') === false
            && in_array($domain, $allowedDomains, true);
        if (!$valid) {
            $errors['email'] = 'Please enter a valid email address (e.g. name@gmail.com).';
        }
    }

    $cleanPhone = preg_replace('/\D/', '', $phoneRaw);
    if (strlen($cleanPhone) === 12 && str_starts_with($cleanPhone, '91')) {
        $cleanPhone = substr($cleanPhone, 2);
    }
    if (!preg_match('/^[6-9][0-9]{9}$/', $cleanPhone)) {
        $errors['phone'] = 'Please enter a valid 10-digit Indian mobile number.';
    }

    if (mb_strlen($address) < 5) {
        $errors['address'] = 'Please enter a valid address.';
    }

    if (strlen($password) < 6) {
        $errors['password'] = 'Password must contain at least 6 characters.';
    } elseif ($password !== $confirm) {
        $errors['confirmPassword'] = 'Passwords do not match!';
    }

    if (!$errors) {
        $stmt = $pdo->prepare("SELECT id FROM customers WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors['email'] = 'This email is already registered. Please login.';
        }
    }

    if (!$errors) {
        $stmt = $pdo->prepare("SELECT id FROM customers WHERE phone = ?");
        $stmt->execute([$cleanPhone]);
        if ($stmt->fetch()) {
            $errors['phone'] = 'This phone number is already registered.';
        }
    }

    if (!$errors) {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare(
            "INSERT INTO customers (full_name, email, password, phone, address, profile_image, status, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, '', 1, NOW(), NOW())"
        );
        $stmt->execute([$fullName, $email, $hash, $cleanPhone, $address]);
        $customerId = $pdo->lastInsertId();

        create_notification($pdo, $customerId, null, 'Welcome to QuickServe!', 'Your account has been created successfully.');

        $_SESSION['customer_id'] = $customerId;
        set_flash('success', 'Account created successfully! Welcome to QuickServe.');
        redirect('index.php');
    }
}

$activePage = '';
$pageTitle = 'Create Account';
require __DIR__ . '/../includes/public_header.php';
?>

<div class="auth-page">
  <div class="auth-card">
    <div class="auth-card-top">
      <div class="logo-box" style="margin:0 auto 12px;">Q</div>
      <h1>Create Account</h1>
      <p>Please fill in your details to register.</p>
    </div>

    <form method="POST" action="">
      <?php echo csrf_field(); ?>

      <div class="form-group">
        <label>Full Name</label>
        <input type="text" name="fullName" placeholder="Enter your full name" value="<?php echo e($old['fullName']); ?>" required>
        <?php if (!empty($errors['fullName'])): ?><span class="form-error"><?php echo e($errors['fullName']); ?></span><?php endif; ?>
      </div>

      <div class="form-group">
        <label>Email Address</label>
        <input type="email" name="email" placeholder="Enter your email" value="<?php echo e($old['email']); ?>" required>
        <?php if (!empty($errors['email'])): ?><span class="form-error"><?php echo e($errors['email']); ?></span><?php endif; ?>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label>Phone Number</label>
          <input type="tel" name="phone" placeholder="+91 98765 43210" maxlength="14" value="<?php echo e($old['phone']); ?>" required>
          <?php if (!empty($errors['phone'])): ?><span class="form-error"><?php echo e($errors['phone']); ?></span><?php endif; ?>
        </div>
        <div class="form-group">
          <label>Address</label>
          <input type="text" name="address" placeholder="Enter your complete address" value="<?php echo e($old['address']); ?>" required>
          <?php if (!empty($errors['address'])): ?><span class="form-error"><?php echo e($errors['address']); ?></span><?php endif; ?>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label>Password</label>
          <input type="password" name="password" placeholder="••••••••" minlength="6" required>
          <?php if (!empty($errors['password'])): ?><span class="form-error"><?php echo e($errors['password']); ?></span><?php endif; ?>
        </div>
        <div class="form-group">
          <label>Confirm Password</label>
          <input type="password" name="confirmPassword" placeholder="••••••••" minlength="6" required>
          <?php if (!empty($errors['confirmPassword'])): ?><span class="form-error"><?php echo e($errors['confirmPassword']); ?></span><?php endif; ?>
        </div>
      </div>

      <button type="submit" class="btn btn-primary btn-block">Create Account</button>
    </form>

    <p class="auth-footer-note">Already have an account? <a href="<?php echo BASE_URL; ?>index.php?showlogin=1">Login</a></p>
  </div>
</div>

<?php require __DIR__ . '/../includes/public_footer.php'; ?>