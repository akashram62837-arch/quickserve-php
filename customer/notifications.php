<?php
require_once __DIR__ . '/../includes/functions.php';
require_customer();

$customerId = $_SESSION['customer_id'];
$stmt = $pdo->prepare("SELECT * FROM customers WHERE id = ?");
$stmt->execute([$customerId]);
$customer = $stmt->fetch();

// Mark all as read when the page is viewed
$pdo->prepare("UPDATE notifications SET is_read = 1 WHERE customer_id = ?")->execute([$customerId]);

$stmt = $pdo->prepare("SELECT * FROM notifications WHERE customer_id = ? ORDER BY created_at DESC");
$stmt->execute([$customerId]);
$notifications = $stmt->fetchAll();

$dashRole = 'customer';
$activeMenu = 'notifications';
$pageTitle = 'Notifications';
$dashUserName = $customer['full_name'];
$dashUserSub = $customer['email'];
require __DIR__ . '/../includes/dash_shell_top.php';
?>

<div class="card">
  <div class="card-header"><h2>Notifications</h2></div>
  <?php if (!$notifications): ?>
    <div class="empty-state"><div>🕭</div><p>You have no notifications yet.</p></div>
  <?php else: ?>
    <?php foreach ($notifications as $n): ?>
      <div style="display:flex;gap:14px;padding:14px 4px;border-bottom:1px solid var(--slate-100);">
        <div style="width:38px;height:38px;border-radius:50%;background:var(--primary-lighter);color:var(--primary);display:flex;align-items:center;justify-content:center;flex-shrink:0;">🕭</div>
        <div>
          <strong style="display:block;font-size:0.92rem;"><?php echo e($n['title']); ?></strong>
          <span style="display:block;font-size:0.85rem;color:var(--slate-600);margin:2px 0 4px;"><?php echo e($n['message']); ?></span>
          <small style="color:var(--slate-400);"><?php echo fdatetime($n['created_at']); ?></small>
        </div>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

<?php require __DIR__ . '/../includes/dash_shell_bottom.php'; ?>
