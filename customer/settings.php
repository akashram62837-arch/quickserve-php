<?php
require_once __DIR__ . '/../includes/functions.php';
require_customer();

$customerId = $_SESSION['customer_id'];
$stmt = $pdo->prepare("SELECT * FROM customers WHERE id = ?");
$stmt->execute([$customerId]);
$customer = $stmt->fetch();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $current = $_POST['current_password'] ?? '';
    $new = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (!verify_password($current, $customer['password'], $pdo, 'customers', $customer['id'])) {
        $errors['current_password'] = 'Current password is incorrect.';
    }
    if (strlen($new) < 6) {
        $errors['new_password'] = 'New password must be at least 6 characters.';
    } elseif ($new !== $confirm) {
        $errors['confirm_password'] = 'Passwords do not match.';
    }

    if (!$errors) {
        $hash = password_hash($new, PASSWORD_BCRYPT);
        $upd = $pdo->prepare("UPDATE customers SET password = ?, updated_at = NOW() WHERE id = ?");
        $upd->execute([$hash, $customerId]);
        set_flash('success', 'Password updated successfully.');
        redirect('customer/settings.php');
    }
}

$dashRole = 'customer';
$activeMenu = 'settings';
$pageTitle = 'Settings';
$dashUserName = $customer['full_name'];
$dashUserSub = $customer['email'];
require __DIR__ . '/../includes/dash_shell_top.php';
?>

<div class="card" style="max-width:520px;">
  <div class="card-header"><h2>Change Password</h2></div>
  <form method="POST" action="">
    <?php echo csrf_field(); ?>
    <div class="form-group">
      <label>Current Password</label>
      <input type="password" name="current_password" required>
      <?php if (!empty($errors['current_password'])): ?><span class="form-error"><?php echo e($errors['current_password']); ?></span><?php endif; ?>
    </div>
    <div class="form-group">
      <label>New Password</label>
      <input type="password" name="new_password" minlength="6" required>
      <?php if (!empty($errors['new_password'])): ?><span class="form-error"><?php echo e($errors['new_password']); ?></span><?php endif; ?>
    </div>
    <div class="form-group">
      <label>Confirm New Password</label>
      <input type="password" name="confirm_password" minlength="6" required>
      <?php if (!empty($errors['confirm_password'])): ?><span class="form-error"><?php echo e($errors['confirm_password']); ?></span><?php endif; ?>
    </div>
    <button type="submit" class="btn btn-primary">Update Password</button>
  </form>
</div>

<?php require __DIR__ . '/../includes/dash_shell_bottom.php'; ?>
