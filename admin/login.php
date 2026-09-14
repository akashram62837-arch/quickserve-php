<?php
require_once __DIR__ . '/../includes/functions.php';

if (is_admin_logged_in()) {
    redirect('admin/dashboard.php');
}

$errors = [];
$oldEmail = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $oldEmail = $email;

    if ($email === '') $errors['email'] = 'Please enter admin email.';
    if ($password === '') $errors['password'] = 'Please enter admin password.';

    if (!$errors) {
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = ?");
        $stmt->execute([$email]);
        $admin = $stmt->fetch();

        if (!$admin || !verify_password($password, $admin['password'], $pdo, 'admins', $admin['id'])) {
            $errors['general'] = 'Invalid admin email or password.';
        } elseif ((int) $admin['status'] === 0) {
            $errors['general'] = 'This admin account has been disabled.';
        } else {
            $_SESSION['admin_id'] = $admin['id'];
            set_flash('success', 'Welcome back, ' . $admin['full_name'] . '!');
            redirect('admin/dashboard.php');
        }
    }
}

$pageTitle = 'Admin Login';
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo e($pageTitle); ?> | QuickServe</title>
<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
</head>
<body>
<div class="auth-page" style="background:linear-gradient(135deg,#0f172a,#1e293b);">
  <div class="auth-card">
    <div class="auth-card-top">
      <div class="logo-box" style="margin:0 auto 12px;background:linear-gradient(135deg,#0f172a,#334155);">Q</div>
      <h1>Admin Login</h1>
      <p>QuickServe Administration Panel</p>
    </div>

    <?php render_flash(); ?>
    <?php if (!empty($errors['general'])): ?><div class="alert alert-error"><?php echo e($errors['general']); ?></div><?php endif; ?>

    <form method="POST" action="">
      <?php echo csrf_field(); ?>
      <div class="form-group">
        <label>Admin Email</label>
        <input type="email" name="email" placeholder="Enter admin email" value="<?php echo e($oldEmail); ?>" required>
        <?php if (!empty($errors['email'])): ?><span class="form-error"><?php echo e($errors['email']); ?></span><?php endif; ?>
      </div>
      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" placeholder="Enter admin password" required>
        <?php if (!empty($errors['password'])): ?><span class="form-error"><?php echo e($errors['password']); ?></span><?php endif; ?>
      </div>
      <button type="submit" class="btn btn-primary btn-block">Login to Admin Panel</button>
    </form>

    <p class="auth-footer-note"><a href="<?php echo BASE_URL; ?>index.php">← Back to QuickServe</a></p>
  </div>
</div>
<script src="<?php echo BASE_URL; ?>assets/js/main.js"></script>
</body>
</html>
