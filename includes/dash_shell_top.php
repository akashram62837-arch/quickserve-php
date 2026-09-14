<?php
/**
 * Shared dashboard shell (sidebar + topbar) used by customer, provider and admin areas.
 *
 * Expected variables set by the including page BEFORE requiring this file:
 *   $dashRole      = 'customer' | 'provider' | 'admin'
 *   $activeMenu    = string key matching one of the nav items below
 *   $pageTitle     = string shown in <title> and topbar heading
 *   $dashUserName  = display name shown top-right and in sidebar footer
 *   $dashUserSub   = small subtext under the name (e.g. email or role)
 */
require_once __DIR__ . '/functions.php';

$dashRole     = $dashRole ?? 'customer';
$activeMenu   = $activeMenu ?? '';
$pageTitle    = $pageTitle ?? 'Dashboard';
$dashUserName = $dashUserName ?? 'User';
$dashUserSub  = $dashUserSub ?? '';

$menus = [
    'customer' => [
        ['key' => 'services',      'label' => 'Categories',     'icon' => '⚒', 'href' => 'services.php'],
        ['key' => 'my-bookings',   'label' => 'My Bookings',    'icon' => '🗒', 'href' => 'my-bookings.php'],
        ['key' => 'payments',      'label' => 'Payments',       'icon' => '💱', 'href' => 'payments.php'],
        ['key' => 'notifications', 'label' => 'Notifications',  'icon' => '🕭', 'href' => 'notifications.php'],
        ['divider' => 'ACCOUNT'],
        ['key' => 'profile',       'label' => 'Profile',        'icon' => '👤', 'href' => 'profile.php'],
        ['key' => 'settings',      'label' => 'Settings',       'icon' => '⚙',  'href' => 'settings.php'],
    ],
    'provider' => [
        ['key' => 'dashboard',     'label' => 'Dashboard',      'icon' => '🏠', 'href' => 'dashboard.php'],
        ['key' => 'my-services',   'label' => 'My Services',    'icon' => '🛠',  'href' => 'my-services.php'],
        ['key' => 'requests',      'label' => 'Booking Requests','icon' => '🗒', 'href' => 'booking-requests.php'],
        ['key' => 'payments',      'label' => 'Payments',       'icon' => '💱', 'href' => 'payments.php'],
        ['key' => 'notifications', 'label' => 'Notifications',  'icon' => '🕭', 'href' => 'notifications.php'],
        ['divider' => 'ACCOUNT'],
        ['key' => 'profile',       'label' => 'Profile',        'icon' => '👤', 'href' => 'profile.php'],
    ],
    'admin' => [
        ['key' => 'dashboard',  'label' => 'Dashboard',        'icon' => '🏠', 'href' => 'dashboard.php'],
        ['key' => 'categories', 'label' => 'Categories',       'icon' => '📂', 'href' => 'categories.php'],
        ['key' => 'providers',  'label' => 'Providers',        'icon' => '🧰', 'href' => 'providers.php'],
        ['key' => 'customers',  'label' => 'Customers',        'icon' => '👥', 'href' => 'customers.php'],
        ['key' => 'bookings',   'label' => 'Bookings',         'icon' => '🗒', 'href' => 'bookings.php'],
    ],
];

$rolePrefix = [
    'customer' => BASE_URL . 'customer/',
    'provider' => BASE_URL . 'provider/',
    'admin'    => BASE_URL . 'admin/',
][$dashRole];

$logoutHref = $rolePrefix . 'logout.php';
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo e($pageTitle); ?> | QuickServe</title>
<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
</head>
<body>

<div class="dash-shell">
  <aside class="dash-sidebar">
    <div class="dash-sidebar-brand">
      <div class="logo-box">Q</div>
      <strong>QuickServe</strong>
    </div>
    <nav class="dash-sidebar-nav">
      <?php foreach ($menus[$dashRole] as $item): ?>
        <?php if (isset($item['divider'])): ?>
          <p style="color:rgba(255,255,255,0.35);font-size:0.7rem;letter-spacing:1px;margin:14px 10px 6px;"><?php echo e($item['divider']); ?></p>
        <?php else: ?>
          <a href="<?php echo $rolePrefix . $item['href']; ?>" class="<?php echo $activeMenu === $item['key'] ? 'active' : ''; ?>">
            <span><?php echo $item['icon']; ?></span> <?php echo e($item['label']); ?>
          </a>
        <?php endif; ?>
      <?php endforeach; ?>
    </nav>
    <div class="dash-sidebar-foot">
      <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px;">
        <div class="customer-profile-avatar"><?php echo e(strtoupper(substr($dashUserName, 0, 1))); ?></div>
        <div style="min-width:0;">
          <strong style="display:block;font-size:0.86rem;color:#fff;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?php echo e($dashUserName); ?></strong>
          <span style="display:block;font-size:0.72rem;color:rgba(255,255,255,0.5);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?php echo e($dashUserSub); ?></span>
        </div>
      </div>
      <button type="button" data-confirm="Are you sure you want to logout?" onclick="window.location.href='<?php echo $logoutHref; ?>'">Logout</button>
    </div>
  </aside>

  <div class="dash-main">
    <div class="dash-topbar">
      <div style="display:flex;align-items:center;gap:12px;">
        <button class="dash-mobile-toggle" type="button">☰</button>
        <h1><?php echo e($pageTitle); ?></h1>
      </div>
      <div class="dash-topbar-user">
        Welcome, <strong><?php echo e($dashUserName); ?></strong>
      </div>
    </div>
    <div class="dash-content">
      <?php render_flash(); ?>
