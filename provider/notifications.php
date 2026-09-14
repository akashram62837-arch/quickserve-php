<?php
require_once __DIR__ . '/../includes/functions.php';
require_provider();

$providerId = $_SESSION['provider_id'];
$stmt = $pdo->prepare("SELECT p.*, c.category_name FROM providers p LEFT JOIN categories c ON c.id = p.category_id WHERE p.id = ?");
$stmt->execute([$providerId]);
$provider = $stmt->fetch();

$pdo->prepare("UPDATE notifications SET is_read = 1 WHERE provider_id = ?")->execute([$providerId]);

$stmt = $pdo->prepare("SELECT * FROM notifications WHERE provider_id = ? ORDER BY created_at DESC");
$stmt->execute([$providerId]);
$notifications = $stmt->fetchAll();

$dashRole = 'provider';
$activeMenu = 'notifications';
$pageTitle = 'Notifications';
$dashUserName = $provider['full_name'];
$dashUserSub = $provider['category_name'];
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
