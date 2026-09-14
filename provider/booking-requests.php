<?php
require_once __DIR__ . '/../includes/functions.php';
require_provider();

$providerId = $_SESSION['provider_id'];
$stmt = $pdo->prepare("SELECT p.*, c.category_name FROM providers p LEFT JOIN categories c ON c.id = p.category_id WHERE p.id = ?");
$stmt->execute([$providerId]);
$provider = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $bookingId = (int) $_POST['booking_id'];
    $newStatus = $_POST['status'] ?? '';

    $allowed = ['Accepted', 'Rejected', 'Completed', 'Cancelled'];
    if (in_array($newStatus, $allowed, true)) {
        $stmt = $pdo->prepare("SELECT * FROM bookings WHERE id = ? AND provider_id = ?");
        $stmt->execute([$bookingId, $providerId]);
        $booking = $stmt->fetch();

        if ($booking) {
            $upd = $pdo->prepare("UPDATE bookings SET booking_status = ?, updated_at = NOW() WHERE id = ?");
            $upd->execute([$newStatus, $bookingId]);

            $msg = [
                'Accepted'  => 'Your booking has been accepted by the provider.',
                'Rejected'  => 'Your booking has been rejected by the provider.',
                'Completed' => 'Your booking has been marked as completed.',
                'Cancelled' => 'Your booking has been cancelled by the provider.',
            ][$newStatus];
            create_notification($pdo, $booking['customer_id'], null, 'Booking ' . $newStatus, $msg);
            set_flash('success', 'Booking marked as ' . $newStatus . '.');
        }
    }
    redirect('provider/booking-requests.php');
}

$statusFilter = $_GET['status'] ?? '';
$sql = "SELECT b.*, s.service_name, cu.full_name AS customer_name, cu.phone AS customer_phone
        FROM bookings b
        LEFT JOIN services s ON s.id = b.service_id
        LEFT JOIN customers cu ON cu.id = b.customer_id
        WHERE b.provider_id = ?";
$params = [$providerId];
if ($statusFilter !== '') {
    $sql .= " AND b.booking_status = ?";
    $params[] = $statusFilter;
}
$sql .= " ORDER BY b.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$bookings = $stmt->fetchAll();

$dashRole = 'provider';
$activeMenu = 'requests';
$pageTitle = 'Booking Requests';
$dashUserName = $provider['full_name'];
$dashUserSub = $provider['category_name'];
require __DIR__ . '/../includes/dash_shell_top.php';
?>

<div class="filter-bar">
  <a href="?status=" class="filter-chip <?php echo $statusFilter === '' ? 'active' : ''; ?>">All</a>
  <a href="?status=Pending" class="filter-chip <?php echo $statusFilter === 'Pending' ? 'active' : ''; ?>">Pending</a>
  <a href="?status=Accepted" class="filter-chip <?php echo $statusFilter === 'Accepted' ? 'active' : ''; ?>">Accepted</a>
  <a href="?status=Completed" class="filter-chip <?php echo $statusFilter === 'Completed' ? 'active' : ''; ?>">Completed</a>
  <a href="?status=Cancelled" class="filter-chip <?php echo $statusFilter === 'Cancelled' ? 'active' : ''; ?>">Cancelled/Rejected</a>
</div>

<div class="card">
  <div class="card-header"><h2>Booking Requests (<?php echo count($bookings); ?>)</h2></div>
  <?php if (!$bookings): ?>
    <div class="empty-state"><div>🗒</div><p>No booking requests found.</p></div>
  <?php else: ?>
    <div class="table-wrap">
      <table class="data-table">
        <thead><tr><th>Customer</th><th>Contact</th><th>Service</th><th>Date &amp; Time</th><th>Address</th><th>Amount</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
          <?php foreach ($bookings as $b): ?>
            <tr>
              <td><?php echo e($b['customer_name']); ?></td>
              <td><?php echo e($b['customer_phone']); ?></td>
              <td><?php echo e($b['service_name']); ?></td>
              <td><?php echo fdate($b['booking_date']); ?><br><small style="color:var(--slate-500);"><?php echo e($b['booking_time']); ?></small></td>
              <td><?php echo e($b['address']); ?></td>
              <td><?php echo money($b['amount']); ?></td>
              <td><span class="badge badge-<?php echo strtolower($b['booking_status']); ?>"><?php echo e($b['booking_status']); ?></span></td>
              <td>
                <div class="action-btns">
                  <?php if ($b['booking_status'] === 'Pending'): ?>
                    <form method="POST" style="display:inline;"><?php echo csrf_field(); ?>
                      <input type="hidden" name="booking_id" value="<?php echo (int) $b['id']; ?>">
                      <input type="hidden" name="status" value="Accepted">
                      <button type="submit" class="icon-btn approve">Accept</button>
                    </form>
                    <form method="POST" style="display:inline;"><?php echo csrf_field(); ?>
                      <input type="hidden" name="booking_id" value="<?php echo (int) $b['id']; ?>">
                      <input type="hidden" name="status" value="Rejected">
                      <button type="submit" class="icon-btn delete" data-confirm="Reject this booking?">Reject</button>
                    </form>
                  <?php elseif ($b['booking_status'] === 'Accepted'): ?>
                    <form method="POST" style="display:inline;"><?php echo csrf_field(); ?>
                      <input type="hidden" name="booking_id" value="<?php echo (int) $b['id']; ?>">
                      <input type="hidden" name="status" value="Completed">
                      <button type="submit" class="icon-btn approve" data-confirm="Mark this booking as completed?">Mark Completed</button>
                    </form>
                  <?php else: ?>
                    <span style="color:var(--slate-400);font-size:0.8rem;">No actions</span>
                  <?php endif; ?>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<?php require __DIR__ . '/../includes/dash_shell_bottom.php'; ?>
