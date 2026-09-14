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

    if ($action === 'toggle_status') {
        $stmt = $pdo->prepare("SELECT status FROM customers WHERE id = ?");
        $stmt->execute([$id]);
        $current = (int) $stmt->fetchColumn();
        $pdo->prepare("UPDATE customers SET status = ?, updated_at = NOW() WHERE id = ?")->execute([$current ? 0 : 1, $id]);
        set_flash('success', 'Customer status updated.');
    } elseif ($action === 'delete') {
        $pdo->prepare("DELETE FROM customers WHERE id = ?")->execute([$id]);
        set_flash('success', 'Customer deleted.');
    }
    redirect('admin/customers.php');
}

$customers = $pdo->query(
    "SELECT c.*, (SELECT COUNT(*) FROM bookings b WHERE b.customer_id = c.id) AS booking_count
     FROM customers c ORDER BY c.created_at DESC"
)->fetchAll();

$dashRole = 'admin';
$activeMenu = 'customers';
$pageTitle = 'Manage Customers';
$dashUserName = $admin['full_name'];
$dashUserSub = 'Administrator';
require __DIR__ . '/../includes/dash_shell_top.php';
?>

<div class="card">
  <div class="card-header"><h2>Customers (<?php echo count($customers); ?>)</h2></div>
  <?php if (!$customers): ?>
    <div class="empty-state"><div>👥</div><p>No customers registered yet.</p></div>
  <?php else: ?>
    <div class="table-wrap">
      <table class="data-table">
        <thead><tr><th>Name</th><th>Contact</th><th>Address</th><th>Bookings</th><th>Joined</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
          <?php foreach ($customers as $c): ?>
            <tr>
              <td><strong><?php echo e($c['full_name']); ?></strong></td>
              <td><?php echo e($c['email']); ?><br><small style="color:var(--slate-500);"><?php echo e($c['phone']); ?></small></td>
              <td><?php echo e($c['address']); ?></td>
              <td><?php echo (int) $c['booking_count']; ?></td>
              <td><?php echo fdate($c['created_at']); ?></td>
              <td><span class="badge badge-<?php echo $c['status'] ? 'active' : 'inactive'; ?>"><?php echo $c['status'] ? 'Active' : 'Disabled'; ?></span></td>
              <td>
                <div class="action-btns">
                  <form method="POST" style="display:inline;"><?php echo csrf_field(); ?>
                    <input type="hidden" name="id" value="<?php echo (int) $c['id']; ?>">
                    <input type="hidden" name="action" value="toggle_status">
                    <button type="submit" class="icon-btn"><?php echo $c['status'] ? 'Disable' : 'Enable'; ?></button>
                  </form>
                  <form method="POST" style="display:inline;"><?php echo csrf_field(); ?>
                    <input type="hidden" name="id" value="<?php echo (int) $c['id']; ?>">
                    <input type="hidden" name="action" value="delete">
                    <button type="submit" class="icon-btn delete" data-confirm="Permanently delete this customer and their bookings?">Delete</button>
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
