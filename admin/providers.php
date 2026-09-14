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

    if ($action === 'approve') {
        $pdo->prepare("UPDATE providers SET is_approved = 1, updated_at = NOW() WHERE id = ?")->execute([$id]);
        create_notification($pdo, null, $id, 'Account Approved', 'Congratulations! Your provider account has been approved. You can now login and add services.');
        set_flash('success', 'Provider approved.');
    } elseif ($action === 'reject') {
        $pdo->prepare("UPDATE providers SET is_approved = 0, status = 0, updated_at = NOW() WHERE id = ?")->execute([$id]);
        set_flash('success', 'Provider registration rejected.');
    } elseif ($action === 'toggle_status') {
        $stmt = $pdo->prepare("SELECT status FROM providers WHERE id = ?");
        $stmt->execute([$id]);
        $current = (int) $stmt->fetchColumn();
        $pdo->prepare("UPDATE providers SET status = ?, updated_at = NOW() WHERE id = ?")->execute([$current ? 0 : 1, $id]);
        set_flash('success', 'Provider status updated.');
    } elseif ($action === 'delete') {
        $pdo->prepare("DELETE FROM providers WHERE id = ?")->execute([$id]);
        set_flash('success', 'Provider deleted.');
    }
    redirect('admin/providers.php');
}

$filter = $_GET['filter'] ?? '';
$sql = "SELECT p.*, c.category_name,
        (SELECT COUNT(*) FROM services s WHERE s.provider_id = p.id) AS service_count,
        (SELECT COUNT(*) FROM bookings b WHERE b.provider_id = p.id) AS booking_count
        FROM providers p LEFT JOIN categories c ON c.id = p.category_id";
if ($filter === 'pending') $sql .= " WHERE p.is_approved = 0";
$sql .= " ORDER BY p.created_at DESC";
$providers = $pdo->query($sql)->fetchAll();

$dashRole = 'admin';
$activeMenu = 'providers';
$pageTitle = 'Manage Providers';
$dashUserName = $admin['full_name'];
$dashUserSub = 'Administrator';
require __DIR__ . '/../includes/dash_shell_top.php';
?>

<div class="filter-bar">
  <a href="?filter=" class="filter-chip <?php echo $filter === '' ? 'active' : ''; ?>">All Providers</a>
  <a href="?filter=pending" class="filter-chip <?php echo $filter === 'pending' ? 'active' : ''; ?>">Pending Approval</a>
</div>

<div class="card">
  <div class="card-header"><h2>Providers (<?php echo count($providers); ?>)</h2></div>
  <?php if (!$providers): ?>
    <div class="empty-state"><div>🧰</div><p>No providers found.</p></div>
  <?php else: ?>
    <div class="table-wrap">
      <table class="data-table">
        <thead><tr><th>Name</th><th>Contact</th><th>Category</th><th>Experience</th><th>Services</th><th>Bookings</th><th>Approval</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
          <?php foreach ($providers as $p): ?>
            <tr>
              <td><strong><?php echo e($p['full_name']); ?></strong></td>
              <td><?php echo e($p['email']); ?><br><small style="color:var(--slate-500);"><?php echo e($p['phone']); ?></small></td>
              <td><?php echo e($p['category_name'] ?? '-'); ?></td>
              <td><?php echo (int) $p['experience']; ?> yrs</td>
              <td><?php echo (int) $p['service_count']; ?></td>
              <td><?php echo (int) $p['booking_count']; ?></td>
              <td><span class="badge badge-<?php echo $p['is_approved'] ? 'approved' : 'pending'; ?>"><?php echo $p['is_approved'] ? 'Approved' : 'Pending'; ?></span></td>
              <td><span class="badge badge-<?php echo $p['status'] ? 'active' : 'inactive'; ?>"><?php echo $p['status'] ? 'Active' : 'Disabled'; ?></span></td>
              <td>
                <div class="action-btns">
                  <?php if (!$p['is_approved']): ?>
                    <form method="POST" style="display:inline;"><?php echo csrf_field(); ?>
                      <input type="hidden" name="id" value="<?php echo (int) $p['id']; ?>">
                      <input type="hidden" name="action" value="approve">
                      <button type="submit" class="icon-btn approve">Approve</button>
                    </form>
                    <form method="POST" style="display:inline;"><?php echo csrf_field(); ?>
                      <input type="hidden" name="id" value="<?php echo (int) $p['id']; ?>">
                      <input type="hidden" name="action" value="reject">
                      <button type="submit" class="icon-btn delete" data-confirm="Reject this provider's registration?">Reject</button>
                    </form>
                  <?php else: ?>
                    <form method="POST" style="display:inline;"><?php echo csrf_field(); ?>
                      <input type="hidden" name="id" value="<?php echo (int) $p['id']; ?>">
                      <input type="hidden" name="action" value="toggle_status">
                      <button type="submit" class="icon-btn"><?php echo $p['status'] ? 'Disable' : 'Enable'; ?></button>
                    </form>
                  <?php endif; ?>
                  <form method="POST" style="display:inline;"><?php echo csrf_field(); ?>
                    <input type="hidden" name="id" value="<?php echo (int) $p['id']; ?>">
                    <input type="hidden" name="action" value="delete">
                    <button type="submit" class="icon-btn delete" data-confirm="Permanently delete this provider and all their services?">Delete</button>
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
