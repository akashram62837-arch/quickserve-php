<?php
require_once __DIR__ . '/../includes/functions.php';
require_customer();

$serviceId = (int) ($_GET['service_id'] ?? $_POST['service_id'] ?? 0);

$stmt = $pdo->prepare(
    "SELECT s.*, c.category_name, p.full_name AS provider_name, p.id AS provider_id
     FROM services s
     LEFT JOIN categories c ON c.id = s.category_id
     LEFT JOIN providers p ON p.id = s.provider_id
     WHERE s.id = ? AND s.is_available = 1"
);
$stmt->execute([$serviceId]);
$service = $stmt->fetch();

if (!$service) {
    set_flash('error', 'This service is not available.');
    redirect('services.php');
}

$customerStmt = $pdo->prepare("SELECT * FROM customers WHERE id = ?");
$customerStmt->execute([$_SESSION['customer_id']]);
$customer = $customerStmt->fetch();

$timeSlots = ['09:00 AM - 11:00 AM', '11:00 AM - 01:00 PM', '01:00 PM - 03:00 PM', '03:00 PM - 05:00 PM', '05:00 PM - 07:00 PM', '07:00 PM - 09:00 PM'];

$errors = [];
$date = '';
$time = '';
$address = $customer['address'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $date = trim($_POST['date'] ?? '');
    $time = trim($_POST['time'] ?? '');
    $address = trim($_POST['address'] ?? '');

    if (!$date || strtotime($date) < strtotime(date('Y-m-d'))) {
        $errors['date'] = 'Please select a valid, upcoming date.';
    }
    if (!in_array($time, $timeSlots, true)) {
        $errors['time'] = 'Please select a preferred time slot.';
    }
    if (mb_strlen($address) < 5) {
        $errors['address'] = 'Please enter a complete service address.';
    }

    if (!$errors) {
        // A provider must be assigned to fulfil the booking. If this catalog
        // service has no specific provider yet, assign an approved provider
        // from the same category (first available), otherwise leave NULL
        // for the admin/provider panel to pick up as an unassigned request.
        $providerId = $service['provider_id'];
        if (!$providerId) {
            $p = $pdo->prepare("SELECT id FROM providers WHERE category_id = ? AND is_approved = 1 AND status = 1 ORDER BY id ASC LIMIT 1");
            $p->execute([$service['category_id']]);
            $found = $p->fetch();
            $providerId = $found ? $found['id'] : null;
        }

        $ins = $pdo->prepare(
            "INSERT INTO bookings (customer_id, provider_id, service_id, booking_date, booking_time, address, amount, booking_status, payment_status, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending', 'Pending', NOW(), NOW())"
        );
        $ins->execute([$customer['id'], $providerId, $service['id'], $date, $time, $address, $service['price']]);
        $bookingId = $pdo->lastInsertId();

        create_notification($pdo, $customer['id'], null, 'Booking Requested', 'Your booking for ' . $service['service_name'] . ' has been submitted and is awaiting confirmation.');
        if ($providerId) {
            create_notification($pdo, null, $providerId, 'New Booking Request', $customer['full_name'] . ' booked ' . $service['service_name'] . '.');
        }

        set_flash('success', 'Booking request submitted! You can track its status in My Bookings.');
        redirect('customer/my-bookings.php');
    }
}

$dashRole = 'customer';
$activeMenu = 'services';
$pageTitle = 'Book Service';
$dashUserName = $customer['full_name'];
$dashUserSub = $customer['email'];
require __DIR__ . '/../includes/dash_shell_top.php';
?>

<div class="booking-wrap">
  <div class="card">
    <div class="card-header"><h2>Booking Details</h2></div>

    <form method="POST" action="">
      <?php echo csrf_field(); ?>
      <input type="hidden" name="service_id" value="<?php echo (int) $service['id']; ?>">

      <div class="form-row">
        <div class="form-group">
          <label>Registered Customer Name</label>
          <input type="text" value="<?php echo e($customer['full_name']); ?>" disabled>
        </div>
        <div class="form-group">
          <label>Registered Mobile Number</label>
          <input type="text" value="<?php echo e($customer['phone']); ?>" disabled>
        </div>
      </div>

      <div class="form-group">
        <label>Registered Email</label>
        <input type="text" value="<?php echo e($customer['email']); ?>" disabled>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label>Service Date *</label>
          <input type="date" name="date" min="<?php echo date('Y-m-d'); ?>" value="<?php echo e($date); ?>" required>
          <?php if (!empty($errors['date'])): ?><span class="form-error"><?php echo e($errors['date']); ?></span><?php endif; ?>
        </div>
        <div class="form-group">
          <label>Preferred Time *</label>
          <select name="time" required>
            <option value="">Select time</option>
            <?php foreach ($timeSlots as $slot): ?>
              <option value="<?php echo e($slot); ?>" <?php echo $time === $slot ? 'selected' : ''; ?>><?php echo e($slot); ?></option>
            <?php endforeach; ?>
          </select>
          <?php if (!empty($errors['time'])): ?><span class="form-error"><?php echo e($errors['time']); ?></span><?php endif; ?>
        </div>
      </div>

      <div class="form-group">
        <label>Service Address *</label>
        <textarea name="address" rows="3" placeholder="Enter your complete address" required><?php echo e($address); ?></textarea>
        <?php if (!empty($errors['address'])): ?><span class="form-error"><?php echo e($errors['address']); ?></span><?php endif; ?>
      </div>

      <button type="submit" class="btn btn-primary btn-block">Confirm Booking</button>
    </form>
  </div>

  <div class="summary-card">
    <h3>Order Summary</h3>
    <div class="service-card-image" style="height:150px;border-radius:10px;overflow:hidden;margin-bottom:14px;">
      <img src="<?php echo service_image_url($service); ?>" alt="<?php echo e($service['service_name']); ?>">
    </div>
    <div class="summary-row"><span>Service</span><strong><?php echo e($service['service_name']); ?></strong></div>
    <div class="summary-row"><span>Category</span><strong><?php echo e($service['category_name']); ?></strong></div>
    <?php if ($service['provider_name']): ?>
      <div class="summary-row"><span>Provider</span><strong><?php echo e($service['provider_name']); ?></strong></div>
    <?php endif; ?>
    <div class="summary-row"><span>Duration</span><strong><?php echo e($service['duration'] ?: 'As required'); ?></strong></div>
    <div class="summary-row total"><span>Total Amount</span><strong><?php echo money($service['price']); ?></strong></div>
  </div>
</div>

<?php require __DIR__ . '/../includes/dash_shell_bottom.php'; ?>
