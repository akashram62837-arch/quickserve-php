<?php
require_once __DIR__ . '/../includes/functions.php';
require_customer();

$customerId = $_SESSION['customer_id'];

$customerStmt = $pdo->prepare("SELECT * FROM customers WHERE id = ?");
$customerStmt->execute([$customerId]);
$customer = $customerStmt->fetch();

/* Handle cancel action */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'cancel') {
    csrf_verify();
    $bookingId = (int) $_POST['booking_id'];
    $stmt = $pdo->prepare("SELECT * FROM bookings WHERE id = ? AND customer_id = ?");
    $stmt->execute([$bookingId, $customerId]);
    $booking = $stmt->fetch();
    if ($booking && !in_array($booking['booking_status'], ['Completed', 'Cancelled'], true)) {
        $upd = $pdo->prepare("UPDATE bookings SET booking_status = 'Cancelled', updated_at = NOW() WHERE id = ?");
        $upd->execute([$bookingId]);
        create_notification($pdo, $customerId, $booking['provider_id'], 'Booking Cancelled', 'A booking has been cancelled by the customer.');
        set_flash('success', 'Booking cancelled successfully.');
    }
    redirect('customer/my-bookings.php');
}

/* Handle review submission */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'review') {
    csrf_verify();
    $bookingId = (int) $_POST['booking_id'];
    $rating = max(1, min(5, (int) $_POST['rating']));
    $reviewText = trim($_POST['review'] ?? '');

    $stmt = $pdo->prepare("SELECT * FROM bookings WHERE id = ? AND customer_id = ? AND booking_status = 'Completed'");
    $stmt->execute([$bookingId, $customerId]);
    $booking = $stmt->fetch();

    if ($booking) {
        $exists = $pdo->prepare("SELECT id FROM reviews WHERE booking_id = ?");
        $exists->execute([$bookingId]);
        if (!$exists->fetch()) {
            $ins = $pdo->prepare(
                "INSERT INTO reviews (booking_id, customer_id, provider_id, service_id, rating, review, created_at, updated_at)
                 VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())"
            );
            $ins->execute([$bookingId, $customerId, $booking['provider_id'], $booking['service_id'], $rating, $reviewText]);
            set_flash('success', 'Thank you for your feedback!');
        }
    }
    redirect('customer/my-bookings.php');
}

$stmt = $pdo->prepare(
    "SELECT b.*, s.service_name, s.service_image, c.category_name, p.full_name AS provider_name, p.phone AS provider_phone,
            r.id AS review_id
     FROM bookings b
     LEFT JOIN services s ON s.id = b.service_id
     LEFT JOIN categories c ON c.id = s.category_id
     LEFT JOIN providers p ON p.id = b.provider_id
     LEFT JOIN reviews r ON r.booking_id = b.id
     WHERE b.customer_id = ?
     ORDER BY b.created_at DESC"
);
$stmt->execute([$customerId]);
$bookings = $stmt->fetchAll();

$dashRole = 'customer';
$activeMenu = 'my-bookings';
$pageTitle = 'My Bookings';
$dashUserName = $customer['full_name'];
$dashUserSub = $customer['email'];
require __DIR__ . '/../includes/dash_shell_top.php';

function booking_badge_class($status) {
    $map = ['Pending' => 'pending', 'Accepted' => 'accepted', 'Completed' => 'completed', 'Cancelled' => 'cancelled', 'Rejected' => 'rejected'];
    return $map[$status] ?? 'pending';
}
?>

<div class="card">
  <div class="card-header"><h2>My Bookings (<?php echo count($bookings); ?>)</h2></div>

  <?php if (!$bookings): ?>
    <div class="empty-state"><div>🗒</div><p>You haven't made any bookings yet.</p>
      <a href="<?php echo BASE_URL; ?>services.php" class="btn btn-primary" style="margin-top:10px;">Browse Services</a>
    </div>
  <?php else: ?>
    <div class="table-wrap">
      <table class="data-table">
        <thead>
          <tr><th>Service</th><th>Provider</th><th>Date &amp; Time</th><th>Address</th><th>Amount</th><th>Status</th><th>Payment</th><th>Actions</th></tr>
        </thead>
        <tbody>
          <?php foreach ($bookings as $b): ?>
            <tr>
              <td><strong><?php echo e($b['service_name'] ?? 'Service'); ?></strong><br><small style="color:var(--slate-500);"><?php echo e($b['category_name']); ?></small></td>
              <td><?php echo e($b['provider_name'] ?? 'To be assigned'); ?></td>
              <td><?php echo fdate($b['booking_date']); ?><br><small style="color:var(--slate-500);"><?php echo e($b['booking_time']); ?></small></td>
              <td><?php echo e($b['address']); ?></td>
              <td><?php echo money($b['amount']); ?></td>
              <td><span class="badge badge-<?php echo booking_badge_class($b['booking_status']); ?>"><?php echo e($b['booking_status']); ?></span></td>
              <td><span class="badge badge-<?php echo strtolower($b['payment_status']) === 'paid' ? 'paid' : 'pending'; ?>"><?php echo e($b['payment_status']); ?></span></td>
              <td>
                <div class="action-btns">
                  <?php if ($b['payment_status'] !== 'Paid' && $b['booking_status'] !== 'Cancelled'): ?>
                    <a class="icon-btn edit" href="<?php echo BASE_URL; ?>customer/payment.php?booking_id=<?php echo (int) $b['id']; ?>">💳 Pay</a>
                  <?php endif; ?>
                  <?php if (!in_array($b['booking_status'], ['Completed', 'Cancelled'], true)): ?>
                    <form method="POST" style="display:inline;">
                      <?php echo csrf_field(); ?>
                      <input type="hidden" name="action" value="cancel">
                      <input type="hidden" name="booking_id" value="<?php echo (int) $b['id']; ?>">
                      <button type="submit" class="icon-btn delete" data-confirm="Cancel this booking?">Cancel</button>
                    </form>
                  <?php endif; ?>
                  <?php if ($b['booking_status'] === 'Completed' && !$b['review_id']): ?>
                    <button type="button" class="icon-btn" onclick="document.getElementById('review-<?php echo $b['id']; ?>').style.display='block'">⭐ Review</button>
                  <?php elseif ($b['review_id']): ?>
                    <span class="badge badge-completed">Reviewed</span>
                  <?php endif; ?>
                </div>

                <?php if ($b['booking_status'] === 'Completed' && !$b['review_id']): ?>
                <div id="review-<?php echo $b['id']; ?>" style="display:none;margin-top:10px;min-width:220px;">
                  <form method="POST">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="action" value="review">
                    <input type="hidden" name="booking_id" value="<?php echo (int) $b['id']; ?>">
                    <select name="rating" required style="margin-bottom:6px;width:100%;padding:6px;border-radius:6px;border:1px solid var(--slate-200);">
                      <option value="5">★★★★★ Excellent</option>
                      <option value="4">★★★★ Good</option>
                      <option value="3">★★★ Average</option>
                      <option value="2">★★ Poor</option>
                      <option value="1">★ Very poor</option>
                    </select>
                    <textarea name="review" rows="2" placeholder="Share your experience..." style="width:100%;padding:6px;border-radius:6px;border:1px solid var(--slate-200);margin-bottom:6px;"></textarea>
                    <button type="submit" class="btn btn-primary btn-sm btn-block">Submit Review</button>
                  </form>
                </div>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<?php require __DIR__ . '/../includes/dash_shell_bottom.php'; ?>
