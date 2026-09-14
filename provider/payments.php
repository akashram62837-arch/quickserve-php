<?php
require_once __DIR__ . '/../includes/functions.php';
require_provider();

$providerId = $_SESSION['provider_id'];
$stmt = $pdo->prepare("SELECT p.*, c.category_name FROM providers p LEFT JOIN categories c ON c.id = p.category_id WHERE p.id = ?");
$stmt->execute([$providerId]);
$provider = $stmt->fetch();

$stmt = $pdo->prepare(
    "SELECT pay.*, s.service_name, cu.full_name AS customer_name
     FROM payments pay
     LEFT JOIN bookings b ON b.id = pay.booking_id
     LEFT JOIN services s ON s.id = b.service_id
     LEFT JOIN customers cu ON cu.id = pay.customer_id
     WHERE b.provider_id = ?
     ORDER BY pay.created_at DESC"
);
$stmt->execute([$providerId]);
$payments = $stmt->fetchAll();

$total = 0; $paidCount = 0; $pendingCount = 0;
foreach ($payments as $p) {
    if ($p['payment_status'] === 'Paid') { $total += (float) $p['amount']; $paidCount++; }
    else { $pendingCount++; }
}

$dashRole = 'provider';
$activeMenu = 'payments';
$pageTitle = 'Payments';
$dashUserName = $provider['full_name'];
$dashUserSub = $provider['category_name'];
require __DIR__ . '/../includes/dash_shell_top.php';
?>

<div class="stat-grid">
  <div class="stat-card"><div class="stat-icon">💰</div><div><strong><?php echo money($total); ?></strong><span>Total Earnings</span></div></div>
  <div class="stat-card"><div class="stat-icon">✅</div><div><strong><?php echo $paidCount; ?></strong><span>Paid</span></div></div>
  <div class="stat-card"><div class="stat-icon">⏳</div><div><strong><?php echo $pendingCount; ?></strong><span>Pending</span></div></div>
</div>

<div class="card">
  <div class="card-header"><h2>Payment History</h2></div>
  <?php if (!$payments): ?>
    <div class="empty-state"><div>💳</div><p>No payments received yet.</p></div>
  <?php else: ?>
    <div class="table-wrap">
      <table class="data-table">
        <thead><tr><th>Transaction ID</th><th>Customer</th><th>Service</th><th>Method</th><th>Amount</th><th>Status</th><th>Date</th></tr></thead>
        <tbody>
          <?php foreach ($payments as $p): ?>
            <tr>
              <td><?php echo e($p['transaction_id']); ?></td>
              <td><?php echo e($p['customer_name']); ?></td>
              <td><?php echo e($p['service_name']); ?></td>
              <td><?php echo e($p['payment_method']); ?></td>
              <td><?php echo money($p['amount']); ?></td>
              <td><span class="badge badge-<?php echo strtolower($p['payment_status']) === 'paid' ? 'paid' : 'pending'; ?>"><?php echo e($p['payment_status']); ?></span></td>
              <td><?php echo fdatetime($p['payment_date'] ?: $p['created_at']); ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<?php require __DIR__ . '/../includes/dash_shell_bottom.php'; ?>
