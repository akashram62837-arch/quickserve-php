<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$stmt = $pdo->prepare("SELECT * FROM admins WHERE id = ?");
$stmt->execute([$_SESSION['admin_id']]);
$admin = $stmt->fetch();

$totalCustomers = (int) $pdo->query("SELECT COUNT(*) FROM customers")->fetchColumn();
$totalProviders = (int) $pdo->query("SELECT COUNT(*) FROM providers")->fetchColumn();
$pendingProviders = (int) $pdo->query("SELECT COUNT(*) FROM providers WHERE is_approved = 0")->fetchColumn();
$totalBookings = (int) $pdo->query("SELECT COUNT(*) FROM bookings")->fetchColumn();
$totalRevenue = (float) $pdo->query("SELECT COALESCE(SUM(amount),0) FROM bookings WHERE payment_status = 'Paid'")->fetchColumn();
$totalCategories = (int) $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();

$recentBookings = $pdo->query(
    "SELECT b.*, s.service_name, cu.full_name AS customer_name, p.full_name AS provider_name
     FROM bookings b
     LEFT JOIN services s ON s.id = b.service_id
     LEFT JOIN customers cu ON cu.id = b.customer_id
     LEFT JOIN providers p ON p.id = b.provider_id
     ORDER BY b.created_at DESC LIMIT 8"
)->fetchAll();

$dashRole = 'admin';
$activeMenu = 'dashboard';
$pageTitle = 'Admin Dashboard';
$dashUserName = $admin['full_name'];
$dashUserSub = 'Administrator';
require __DIR__ . '/../includes/dash_shell_top.php';
?>

<div class="stat-grid">
  <div class="stat-card"><div class="stat-icon">👥</div><div><strong><?php echo $totalCustomers; ?></strong><span>Customers</span></div></div>
  <div class="stat-card"><div class="stat-icon">🧰</div><div><strong><?php echo $totalProviders; ?></strong><span>Providers</span></div></div>
  <div class="stat-card"><div class="stat-icon">🗒</div><div><strong><?php echo $totalBookings; ?></strong><span>Total Bookings</span></div></div>
  <div class="stat-card"><div class="stat-icon">💰</div><div><strong><?php echo money($totalRevenue); ?></strong><span>Total Revenue</span></div></div>
</div>

<div class="stat-grid" style="grid-template-columns:repeat(2,1fr);">
  <div class="stat-card"><div class="stat-icon">📂</div><div><strong><?php echo $totalCategories; ?></strong><span>Service Categories</span></div></div>
  <div class="stat-card"><div class="stat-icon">⏳</div><div><strong><?php echo $pendingProviders; ?></strong><span>Providers Awaiting Approval</span></div>
    <?php if ($pendingProviders > 0): ?><a href="<?php echo BASE_URL; ?>admin/providers.php?filter=pending" class="btn btn-sm btn-outline" style="margin-left:auto;">Review</a><?php endif; ?>
  </div>
</div>

<div class="card">
  <div class="card-header">
    <h2>Recent Bookings</h2>
    <a href="<?php echo BASE_URL; ?>admin/bookings.php" class="btn btn-outline btn-sm">View All</a>
  </div>
  <?php if (!$recentBookings): ?>
    <div class="empty-state"><div>🗒</div><p>No bookings yet.</p></div>
  <?php else: ?>
    <div class="table-wrap">
      <table class="data-table">
        <thead><tr><th>Customer</th><th>Provider</th><th>Service</th><th>Date</th><th>Amount</th><th>Status</th><th>Payment</th></tr></thead>
        <tbody>
        <?php foreach ($recentBookings as $b): ?>
          <tr>
            <td><?php echo e($b['customer_name']); ?></td>
            <td><?php echo e($b['provider_name'] ?? 'Unassigned'); ?></td>
            <td><?php echo e($b['service_name']); ?></td>
            <td><?php echo fdate($b['booking_date']); ?></td>
            <td><?php echo money($b['amount']); ?></td>
            <td><span class="badge badge-<?php echo strtolower($b['booking_status']); ?>"><?php echo e($b['booking_status']); ?></span></td>
            <td><span class="badge badge-<?php echo strtolower($b['payment_status']) === 'paid' ? 'paid' : 'pending'; ?>"><?php echo e($b['payment_status']); ?></span></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<?php require __DIR__ . '/../includes/dash_shell_bottom.php'; ?>
