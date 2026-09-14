<?php
require_once __DIR__ . '/../includes/functions.php';

if (is_provider_logged_in()) {
    redirect('provider/dashboard.php');
}

$errors = [];
$oldEmail = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $oldEmail = $email;

    if ($email === '' || $password === '') {
        $errors['general'] = 'Please enter your email and password.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM providers WHERE email = ?");
        $stmt->execute([$email]);
        $provider = $stmt->fetch();

        if (!$provider) {
            $errors['general'] = 'No provider account found with this email.';
        } elseif (!verify_password($password, $provider['password'], $pdo, 'providers', $provider['id'])) {
            $errors['general'] = 'Incorrect password. Please try again.';
        } elseif ((int) $provider['is_approved'] === 0) {
            $errors['general'] = 'Your account is pending admin approval. Please check back later.';
        } elseif ((int) $provider['status'] === 0) {
            $errors['general'] = 'Your account has been disabled. Please contact support.';
        } else {
            $_SESSION['provider_id'] = $provider['id'];
            set_flash('success', 'Welcome back, ' . $provider['full_name'] . '!');
            redirect('provider/dashboard.php');
        }
    }
}

$pageTitle = 'Provider Login';
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
  <div class="auth-card">
    <a href="<?php echo BASE_URL; ?>index.php" class="auth-back-link">← Back to Home</a>
    <div class="auth-card-top">
      <div class="logo-box" style="margin:0 auto 12px;">Q</div>
      <h1>Provider Login</h1>
      <p>Access your QuickServe provider dashboard.</p>
    </div>

    <?php render_flash(); ?>
    <?php if (!empty($errors['general'])): ?><div class="alert alert-error"><?php echo e($errors['general']); ?></div><?php endif; ?>

    <form method="POST" action="">
      <?php echo csrf_field(); ?>
      <div class="form-group">
        <label>Email Address</label>
        <input type="email" name="email" placeholder="Enter your email" value="<?php echo e($oldEmail); ?>" required>
      </div>
      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" placeholder="Enter your password" required>
      </div>
      <button type="submit" class="btn btn-primary btn-block">Login</button>
    </form>

    <p class="auth-footer-note">New provider? <a href="<?php echo BASE_URL; ?>provider/register.php">Register here</a></p>
  </div>
</div>
<script src="<?php echo BASE_URL; ?>assets/js/main.js"></script>
</body>
</html>
