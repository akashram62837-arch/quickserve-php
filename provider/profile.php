<?php
require_once __DIR__ . '/../includes/functions.php';
require_provider();

$providerId = $_SESSION['provider_id'];
$stmt = $pdo->prepare("SELECT * FROM providers WHERE id = ?");
$stmt->execute([$providerId]);
$provider = $stmt->fetch();

$categories = $pdo->query("SELECT * FROM categories WHERE status = 1 ORDER BY category_name ASC")->fetchAll();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['form'] ?? '') === 'profile') {
    csrf_verify();
    $fullName = trim($_POST['fullName'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $categoryId = (int) ($_POST['category_id'] ?? 0);

    if (mb_strlen($fullName) < 2) $errors['fullName'] = 'Please enter a valid full name.';
    if (!preg_match('/^\d{10}$/', $phone)) $errors['phone'] = 'Please enter a valid 10-digit phone number.';

    if (!$errors) {
        $upd = $pdo->prepare("UPDATE providers SET full_name = ?, phone = ?, address = ?, category_id = ?, updated_at = NOW() WHERE id = ?");
        $upd->execute([$fullName, $phone, $address, $categoryId, $providerId]);
        set_flash('success', 'Profile updated successfully.');
        redirect('provider/profile.php');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['form'] ?? '') === 'password') {
    csrf_verify();
    $current = $_POST['current_password'] ?? '';
    $new = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (!verify_password($current, $provider['password'], $pdo, 'providers', $provider['id'])) {
        $errors['current_password'] = 'Current password is incorrect.';
    } elseif (strlen($new) < 6) {
        $errors['new_password'] = 'New password must be at least 6 characters.';
    } elseif ($new !== $confirm) {
        $errors['confirm_password'] = 'Passwords do not match.';
    }

    if (!$errors) {
        $hash = password_hash($new, PASSWORD_BCRYPT);
        $pdo->prepare("UPDATE providers SET password = ?, updated_at = NOW() WHERE id = ?")->execute([$hash, $providerId]);
        set_flash('success', 'Password updated successfully.');
        redirect('provider/profile.php');
    }
}

$dashRole = 'provider';
$activeMenu = 'profile';
$pageTitle = 'My Profile';
$dashUserName = $provider['full_name'];
$dashUserSub = $provider['email'];
require __DIR__ . '/../includes/dash_shell_top.php';
?>

<div class="card" style="max-width:640px;">
  <div class="card-header"><h2>Profile Information</h2>
    <span class="badge badge-<?php echo $provider['is_approved'] ? 'approved' : 'pending'; ?>"><?php echo $provider['is_approved'] ? 'Approved' : 'Pending Approval'; ?></span>
  </div>
  <form method="POST" action="">
    <?php echo csrf_field(); ?>
    <input type="hidden" name="form" value="profile">
    <div class="form-group">
      <label>Full Name</label>
      <input type="text" name="fullName" value="<?php echo e($provider['full_name']); ?>" required>
      <?php if (!empty($errors['fullName'])): ?><span class="form-error"><?php echo e($errors['fullName']); ?></span><?php endif; ?>
    </div>
    <div class="form-group">
      <label>Email Address</label>
      <input type="email" value="<?php echo e($provider['email']); ?>" disabled>
      <span class="form-help">Email address cannot be changed.</span>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label>Phone Number</label>
        <input type="text" name="phone" value="<?php echo e($provider['phone']); ?>" required>
        <?php if (!empty($errors['phone'])): ?><span class="form-error"><?php echo e($errors['phone']); ?></span><?php endif; ?>
      </div>
      <div class="form-group">
        <label>Service Category</label>
        <select name="category_id">
          <?php foreach ($categories as $cat): ?>
            <option value="<?php echo (int) $cat['id']; ?>" <?php echo (int) $provider['category_id'] === (int) $cat['id'] ? 'selected' : ''; ?>><?php echo e($cat['category_name']); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
    <div class="form-group">
      <label>Address</label>
      <textarea name="address" rows="2"><?php echo e($provider['address']); ?></textarea>
    </div>
    <div class="form-group">
      <label>Aadhar Number</label>
      <input type="text" value="<?php echo e($provider['aadhaar_number']); ?>" disabled>
    </div>
    <div class="form-group">
      <label>Experience</label>
      <input type="text" value="<?php echo (int) $provider['experience']; ?> years" disabled>
    </div>
    <button type="submit" class="btn btn-primary">Save Changes</button>
  </form>
</div>

<div class="card" style="max-width:520px;">
  <div class="card-header"><h2>Change Password</h2></div>
  <form method="POST" action="">
    <?php echo csrf_field(); ?>
    <input type="hidden" name="form" value="password">
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
