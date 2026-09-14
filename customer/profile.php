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
    $fullName = trim($_POST['fullName'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');

    if (mb_strlen($fullName) < 2) $errors['fullName'] = 'Please enter a valid full name.';
    if (!preg_match('/^[6-9][0-9]{9}$/', preg_replace('/\D/', '', $phone))) $errors['phone'] = 'Please enter a valid 10-digit mobile number.';

    if (!$errors) {
        $upd = $pdo->prepare("UPDATE customers SET full_name = ?, phone = ?, address = ?, updated_at = NOW() WHERE id = ?");
        $upd->execute([$fullName, preg_replace('/\D/', '', $phone), $address, $customerId]);
        set_flash('success', 'Profile updated successfully.');
        redirect('customer/profile.php');
    }
}

$dashRole = 'customer';
$activeMenu = 'profile';
$pageTitle = 'My Profile';
$dashUserName = $customer['full_name'];
$dashUserSub = $customer['email'];
require __DIR__ . '/../includes/dash_shell_top.php';
?>

<div class="card" style="max-width:640px;">
  <div class="card-header"><h2>Profile Information</h2></div>
  <form method="POST" action="">
    <?php echo csrf_field(); ?>
    <div class="form-group">
      <label>Full Name</label>
      <input type="text" name="fullName" value="<?php echo e($customer['full_name']); ?>" required>
      <?php if (!empty($errors['fullName'])): ?><span class="form-error"><?php echo e($errors['fullName']); ?></span><?php endif; ?>
    </div>
    <div class="form-group">
      <label>Email Address</label>
      <input type="email" value="<?php echo e($customer['email']); ?>" disabled>
      <span class="form-help">Email address cannot be changed.</span>
    </div>
    <div class="form-group">
      <label>Phone Number</label>
      <input type="text" name="phone" value="<?php echo e($customer['phone']); ?>" required>
      <?php if (!empty($errors['phone'])): ?><span class="form-error"><?php echo e($errors['phone']); ?></span><?php endif; ?>
    </div>
    <div class="form-group">
      <label>Address</label>
      <textarea name="address" rows="3"><?php echo e($customer['address']); ?></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Save Changes</button>
  </form>
</div>

<?php require __DIR__ . '/../includes/dash_shell_bottom.php'; ?>
