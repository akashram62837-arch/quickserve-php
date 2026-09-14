<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$stmt = $pdo->prepare("SELECT * FROM admins WHERE id = ?");
$stmt->execute([$_SESSION['admin_id']]);
$admin = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $id = (int) $_POST['id'];
    $action = $_POST['action'] ?? '';

    if ($action === 'set_status') {
        $status = $_POST['status'] ?? '';
        if (in_array($status, ['Pending', 'Accepted', 'Completed', 'Cancelled', 'Rejected'], true)) {
            $pdo->prepare("UPDATE bookings SET booking_status = ?, updated_at = NOW() WHERE id = ?")->execute([$status, $id]);
            set_flash('success', 'Booking status updated.');
        }
    } elseif ($action === 'delete') {
        $pdo->prepare("DELETE FROM bookings WHERE id = ?")->execute([$id]);
        set_flash('success', 'Booking deleted.');
    }
    redirect('admin/bookings.php');
}

$statusFilter = $_GET['status'] ?? '';
$sql = "SELECT b.*, s.service_name, cu.full_name AS customer_name, p.full_name AS provider_name
        FROM bookings b
        LEFT JOIN services s ON s.id = b.service_id
        LEFT JOIN customers cu ON cu.id = b.customer_id
        LEFT JOIN providers p ON p.id = b.provider_id";
$params = [];
if ($statusFilter !== '') {
    $sql .= " WHERE b.booking_status = ?";
    $params[] = $statusFilter;
}
$sql .= " ORDER BY b.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$bookings = $stmt->fetchAll();

$dashRole = 'admin';
$activeMenu = 'bookings';
$pageTitle = 'Manage Bookings';
$dashUserName = $admin['full_name'];
$dashUserSub = 'Administrator';
require __DIR__ . '/../includes/dash_shell_top.php';
?>

<div class="filter-bar">
  <a href="?status=" class="filter-chip <?php echo $statusFilter === '' ? 'active' : ''; ?>">All</a>
  <a href="?status=Pending" class="filter-chip <?php echo $statusFilter === 'Pending' ? 'active' : ''; ?>">Pending</a>
  <a href="?status=Accepted" class="filter-chip <?php echo $statusFilter === 'Accepted' ? 'active' : ''; ?>">Accepted</a>
  <a href="?status=Completed" class="filter-chip <?php echo $statusFilter === 'Completed' ? 'active' : ''; ?>">Completed</a>
  <a href="?status=Cancelled" class="filter-chip <?php echo $statusFilter === 'Cancelled' ? 'active' : ''; ?>">Cancelled</a>
</div>

<div class="card">
  <div class="card-header"><h2>Bookings (<?php echo count($bookings); ?>)</h2></div>
  <?php if (!$bookings): ?>
    <div class="empty-state"><div>🗒</div><p>No bookings found.</p></div>
  <?php else: ?>
    <div class="table-wrap">
      <table class="data-table">
        <thead><tr><th>#</th><th>Customer</th><th>Provider</th><th>Service</th><th>Date &amp; Time</th><th>Amount</th><th>Status</th><th>Payment</th><th>Actions</th></tr></thead>
        <tbody>
          <?php foreach ($bookings as $b): ?>
            <tr>
              <td>#<?php echo (int) $b['id']; ?></td>
              <td><?php echo e($b['customer_name']); ?></td>
              <td><?php echo e($b['provider_name'] ?? 'Unassigned'); ?></td>
              <td><?php echo e($b['service_name']); ?></td>
              <td><?php echo fdate($b['booking_date']); ?><br><small style="color:var(--slate-500);"><?php echo e($b['booking_time']); ?></small></td>
              <td><?php echo money($b['amount']); ?></td>
              <td><span class="badge badge-<?php echo strtolower($b['booking_status']); ?>"><?php echo e($b['booking_status']); ?></span></td>
              <td><span class="badge badge-<?php echo strtolower($b['payment_status']) === 'paid' ? 'paid' : 'pending'; ?>"><?php echo e($b['payment_status']); ?></span></td>
              <td>
                <div class="action-btns">
                  <form method="POST" style="display:inline;">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="action" value="set_status">
                    <input type="hidden" name="id" value="<?php echo (int) $b['id']; ?>">
                    <select name="status" onchange="this.form.submit()" style="padding:6px;border-radius:6px;border:1px solid var(--slate-200);font-size:0.78rem;">
                      <?php foreach (['Pending','Accepted','Completed','Cancelled','Rejected'] as $st): ?>
                        <option value="<?php echo $st; ?>" <?php echo $b['booking_status'] === $st ? 'selected' : ''; ?>><?php echo $st; ?></option>
                      <?php endforeach; ?>
                    </select>
                  </form>
                  <form method="POST" style="display:inline;">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?php echo (int) $b['id']; ?>">
                    <button type="submit" class="icon-btn delete" data-confirm="Delete this booking record?">Delete</button>
                  </form>
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
