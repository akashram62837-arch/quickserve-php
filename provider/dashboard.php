<?php
require_once __DIR__ . '/../includes/functions.php';
require_provider();

$providerId = $_SESSION['provider_id'];
$stmt = $pdo->prepare("SELECT p.*, c.category_name FROM providers p LEFT JOIN categories c ON c.id = p.category_id WHERE p.id = ?");
$stmt->execute([$providerId]);
$provider = $stmt->fetch();

if (!$provider) { redirect('provider/logout.php'); }

$totalBookings = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE provider_id = ?");
$totalBookings->execute([$providerId]);
$totalBookings = (int) $totalBookings->fetchColumn();

$pendingRequests = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE provider_id = ? AND booking_status = 'Pending'");
$pendingRequests->execute([$providerId]);
$pendingRequests = (int) $pendingRequests->fetchColumn();

$completedJobs = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE provider_id = ? AND booking_status = 'Completed'");
$completedJobs->execute([$providerId]);
$completedJobs = (int) $completedJobs->fetchColumn();

$earnings = $pdo->prepare("SELECT COALESCE(SUM(amount),0) FROM bookings WHERE provider_id = ? AND payment_status = 'Paid'");
$earnings->execute([$providerId]);
$earnings = (float) $earnings->fetchColumn();

$avgRating = $pdo->prepare("SELECT ROUND(AVG(rating),1) FROM reviews WHERE provider_id = ?");
$avgRating->execute([$providerId]);
$avgRating = $avgRating->fetchColumn();

$recentBookings = $pdo->prepare(
    "SELECT b.*, s.service_name, cu.full_name AS customer_name
     FROM bookings b
     LEFT JOIN services s ON s.id = b.service_id
     LEFT JOIN customers cu ON cu.id = b.customer_id
     WHERE b.provider_id = ?
     ORDER BY b.created_at DESC LIMIT 6"
);
$recentBookings->execute([$providerId]);
$recentBookings = $recentBookings->fetchAll();

$dashRole = 'provider';
$activeMenu = 'dashboard';
$pageTitle = 'Provider Dashboard';
$dashUserName = $provider['full_name'];
$dashUserSub = $provider['category_name'];
require __DIR__ . '/../includes/dash_shell_top.php';
?>

<?php if (!(int) $provider['is_approved']): ?>
  <div class="alert alert-info">Your account is pending admin approval. Some features are limited until approval.</div>
<?php endif; ?>

<div class="stat-grid">
  <div class="stat-card"><div class="stat-icon">🗒</div><div><strong><?php echo $totalBookings; ?></strong><span>Total Bookings</span></div></div>
  <div class="stat-card"><div class="stat-icon">⏳</div><div><strong><?php echo $pendingRequests; ?></strong><span>Pending Requests</span></div></div>
  <div class="stat-card"><div class="stat-icon">✅</div><div><strong><?php echo $completedJobs; ?></strong><span>Completed Jobs</span></div></div>
  <div class="stat-card"><div class="stat-icon">💰</div><div><strong><?php echo money($earnings); ?></strong><span>Total Earnings</span></div></div>
</div>

<div class="card">
  <div class="card-header">
    <h2>Recent Bookings</h2>
    <a href="<?php echo BASE_URL; ?>provider/booking-requests.php" class="btn btn-outline btn-sm">View All</a>
  </div>
  <?php if (!$recentBookings): ?>
    <div class="empty-state"><div>🗒</div><p>No bookings yet.</p></div>
  <?php else: ?>
    <div class="table-wrap">
      <table class="data-table">
        <thead><tr><th>Customer</th><th>Service</th><th>Date</th><th>Amount</th><th>Status</th></tr></thead>
        <tbody>
        <?php foreach ($recentBookings as $b): ?>
          <tr>
            <td><?php echo e($b['customer_name']); ?></td>
            <td><?php echo e($b['service_name']); ?></td>
            <td><?php echo fdate($b['booking_date']); ?></td>
            <td><?php echo money($b['amount']); ?></td>
            <td><span class="badge badge-<?php echo strtolower($b['booking_status']); ?>"><?php echo e($b['booking_status']); ?></span></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<div class="card">
  <div class="card-header"><h2>Rating</h2></div>
  <p style="font-size:1.4rem;color:#f59e0b;"><?php echo render_stars($avgRating ?: 0); ?> <span style="color:var(--slate-500);font-size:0.9rem;">(<?php echo $avgRating ?: 'No ratings yet'; ?>)</span></p>
</div>

<?php require __DIR__ . '/../includes/dash_shell_bottom.php'; ?>
