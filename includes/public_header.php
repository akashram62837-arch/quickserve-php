<?php
/**
 * Shared public header/navbar.
 * Expects (optionally) $activePage to be set by the including page, e.g. 'home' | 'services' | 'about'.
 */
require_once __DIR__ . '/functions.php';
$activePage = $activePage ?? '';

$loggedInCustomer = null;
if (is_customer_logged_in()) {
    $stmt = $pdo->prepare("SELECT * FROM customers WHERE id = ?");
    $stmt->execute([$_SESSION['customer_id']]);
    $loggedInCustomer = $stmt->fetch();
}
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo isset($pageTitle) ? e($pageTitle) . ' | QuickServe' : 'QuickServe — Home services at your doorstep'; ?></title>
<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
</head>
<body>

<header class="qs-header">
  <div class="qs-header-row">

    <a href="<?php echo BASE_URL; ?>index.php" class="qs-logo">
      <div class="logo-box">Q</div>
      <div><strong>QuickServe</strong></div>
    </a>

    <nav class="qs-nav">
      <a href="<?php echo BASE_URL; ?>index.php" class="qs-nav-link <?php echo $activePage === 'home' ? 'active' : ''; ?>">Home</a>
      <a href="<?php echo BASE_URL; ?>services.php" class="qs-nav-link <?php echo $activePage === 'services' ? 'active' : ''; ?>">Services</a>
      <a href="<?php echo BASE_URL; ?>about.php" class="qs-nav-link <?php echo $activePage === 'about' ? 'active' : ''; ?>">About</a>
      <a href="<?php echo BASE_URL; ?>index.php#contact" class="qs-nav-link">Contact</a>
    </nav>

    <div class="header-actions">

      <?php if (!$loggedInCustomer): ?>
        <a href="<?php echo BASE_URL; ?>provider/register.php" class="header-provider">For Provider</a>
      <?php endif; ?>

      <?php if ($loggedInCustomer): ?>
        <div class="customer-account-wrapper">
          <button class="customer-profile-button" type="button">
            <div class="customer-profile-avatar"><?php echo e(strtoupper(substr($loggedInCustomer['full_name'], 0, 1))); ?></div>
            <span class="customer-profile-name"><?php echo e($loggedInCustomer['full_name']); ?></span>
            <span class="customer-profile-arrow">▼</span>
          </button>
          <div class="customer-account-dropdown" style="display:none;">
            <div class="customer-dropdown-user">
              <div class="customer-dropdown-avatar"><?php echo e(strtoupper(substr($loggedInCustomer['full_name'], 0, 1))); ?></div>
              <div>
                <strong><?php echo e($loggedInCustomer['full_name']); ?></strong>
                <span><?php echo e($loggedInCustomer['email']); ?></span>
              </div>
            </div>
            <div class="customer-dropdown-divider"></div>
            <a class="customer-dropdown-item" href="<?php echo BASE_URL; ?>customer/my-bookings.php">
              <span>🗒</span><div><strong>My Bookings</strong><small>View your bookings</small></div>
            </a>
            <a class="customer-dropdown-item" href="<?php echo BASE_URL; ?>customer/notifications.php">
              <span>🕭</span><div><strong>Notifications</strong><small>View notifications</small></div>
            </a>
            <a class="customer-dropdown-item" href="<?php echo BASE_URL; ?>customer/profile.php">
              <span>👤</span><div><strong>My Profile</strong><small>Manage your profile</small></div>
            </a>
            <div class="customer-dropdown-divider"></div>
            <a class="customer-dropdown-logout" href="<?php echo BASE_URL; ?>customer/logout.php">
              <span>➜</span><strong>Logout</strong>
            </a>
          </div>
        </div>
      <?php else: ?>
        <button class="header-login" type="button" data-open-login>Login</button>
      <?php endif; ?>

      <button class="header-menu-toggle" type="button" aria-label="Menu">☰</button>
    </div>
  </div>
</header>

<?php if (!$loggedInCustomer): ?>
<div class="login-overlay" id="loginOverlay" style="display:none;">
  <div class="login-modal" onclick="event.stopPropagation()">
    <button class="login-close" type="button" data-close-login>&times;</button>
    <div class="login-header">
      <div class="login-logo">Q</div>
      <h2>Welcome Back</h2>
      <p>Login to your QuickServe account</p>
    </div>
    <form action="<?php echo BASE_URL; ?>customer/login.php" method="POST">
      <?php echo csrf_field(); ?>
      <input type="hidden" name="redirect" value="<?php echo e($_SERVER['REQUEST_URI'] ?? ''); ?>">
      <div class="login-form-group">
        <label>Email Address</label>
        <input type="email" name="email" placeholder="Enter your email" required>
      </div>
      <div class="login-form-group">
        <label>Password</label>
        <input type="password" name="password" placeholder="Enter your password" required>
      </div>
      <button type="submit" class="btn btn-primary login-submit">Login</button>
    </form>
    <div class="login-register">
      <span>Don't have an account?</span>
      <a href="<?php echo BASE_URL; ?>customer/register.php" data-close-login>Register</a>
    </div>
  </div>
</div>
<?php endif; ?>

<main class="main-content">
<?php render_flash(); ?>
