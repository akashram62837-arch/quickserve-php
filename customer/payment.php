<?php
require_once __DIR__ . '/../includes/functions.php';
require_customer();

$customerId = $_SESSION['customer_id'];
$customerStmt = $pdo->prepare("SELECT * FROM customers WHERE id = ?");
$customerStmt->execute([$customerId]);
$customer = $customerStmt->fetch();

$bookingId = (int) ($_GET['booking_id'] ?? $_POST['booking_id'] ?? 0);
$stmt = $pdo->prepare(
    "SELECT b.*, s.service_name, s.service_image, c.category_name
     FROM bookings b
     LEFT JOIN services s ON s.id = b.service_id
     LEFT JOIN categories c ON c.id = s.category_id
     WHERE b.id = ? AND b.customer_id = ?"
);
$stmt->execute([$bookingId, $customerId]);
$booking = $stmt->fetch();

if (!$booking) {
    set_flash('error', 'Booking not found.');
    redirect('customer/my-bookings.php');
}
if ($booking['payment_status'] === 'Paid') {
    set_flash('info', 'This booking has already been paid for.');
    redirect('customer/my-bookings.php');
}
if ($booking['booking_status'] === 'Cancelled') {
    set_flash('error', 'This booking has been cancelled and cannot be paid.');
    redirect('customer/my-bookings.php');
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $method = $_POST['payment_method'] ?? '';
    if (!in_array($method, ['UPI', 'Card', 'Cash'], true)) {
        $errors['method'] = 'Please select a payment method.';
    }

    if (!$errors) {
        $pdo->beginTransaction();
        try {
            $status = $method === 'Cash' ? 'Pending' : 'Paid';
            $txn = generate_transaction_id();

            $ins = $pdo->prepare(
                "INSERT INTO payments (booking_id, customer_id, payment_method, transaction_id, amount, payment_status, payment_date, created_at, updated_at)
                 VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW(), NOW())"
            );
            $ins->execute([$booking['id'], $customerId, $method, $txn, $booking['amount'], $status]);

            $upd = $pdo->prepare("UPDATE bookings SET payment_status = ?, updated_at = NOW() WHERE id = ?");
            $upd->execute([$status, $booking['id']]);

            create_notification($pdo, $customerId, $booking['provider_id'],
                $status === 'Paid' ? 'Payment Received' : 'Payment Pending',
                $status === 'Paid'
                    ? 'Your payment for ' . $booking['service_name'] . ' has been received.'
                    : 'Cash payment selected for ' . $booking['service_name'] . '. Please pay the provider on service completion.'
            );

            $pdo->commit();
            set_flash('success', $status === 'Paid' ? 'Payment successful! Transaction ID: ' . $txn : 'Booking confirmed. Please pay cash on service.');
            redirect('customer/my-bookings.php');
        } catch (Exception $e) {
            $pdo->rollBack();
            $errors['general'] = 'Payment could not be processed. Please try again.';
        }
    }
}

$dashRole = 'customer';
$activeMenu = 'my-bookings';
$pageTitle = 'Payment';
$dashUserName = $customer['full_name'];
$dashUserSub = $customer['email'];
require __DIR__ . '/../includes/dash_shell_top.php';
?>

<div class="payment-wrap">
  <div class="card">
    <div class="card-header"><h2>Choose Payment Method</h2></div>

    <?php if (!empty($errors['general'])): ?><div class="alert alert-error"><?php echo e($errors['general']); ?></div><?php endif; ?>

    <form method="POST" action="">
      <?php echo csrf_field(); ?>
      <input type="hidden" name="booking_id" value="<?php echo (int) $booking['id']; ?>">

      <div class="pay-method-list">
        <label class="pay-method-option">
          <input type="radio" name="payment_method" value="UPI" required> 📱 UPI (Google Pay / PhonePe / Paytm)
        </label>
        <label class="pay-method-option">
          <input type="radio" name="payment_method" value="Card"> 💳 Credit / Debit Card
        </label>
        <label class="pay-method-option">
          <input type="radio" name="payment_method" value="Cash"> 💵 Cash on Service
        </label>
      </div>
      <?php if (!empty($errors['method'])): ?><span class="form-error"><?php echo e($errors['method']); ?></span><?php endif; ?>

      <button type="submit" class="btn btn-primary btn-block">Pay <?php echo money($booking['amount']); ?></button>
    </form>
  </div>

  <div class="summary-card">
    <h3>Booking Summary</h3>
    <div class="summary-row"><span>Service</span><strong><?php echo e($booking['service_name']); ?></strong></div>
    <div class="summary-row"><span>Category</span><strong><?php echo e($booking['category_name']); ?></strong></div>
    <div class="summary-row"><span>Date</span><strong><?php echo fdate($booking['booking_date']); ?></strong></div>
    <div class="summary-row"><span>Time</span><strong><?php echo e($booking['booking_time']); ?></strong></div>
    <div class="summary-row total"><span>Amount Payable</span><strong><?php echo money($booking['amount']); ?></strong></div>
  </div>
</div>

<?php require __DIR__ . '/../includes/dash_shell_bottom.php'; ?>
