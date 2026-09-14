<?php
require_once __DIR__ . '/../includes/functions.php';
require_provider();

$providerId = $_SESSION['provider_id'];
$stmt = $pdo->prepare("SELECT p.*, c.category_name FROM providers p LEFT JOIN categories c ON c.id = p.category_id WHERE p.id = ?");
$stmt->execute([$providerId]);
$provider = $stmt->fetch();

$errors = [];

/* Handle create/update */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'save') {
    csrf_verify();

    $serviceId   = (int) ($_POST['service_id'] ?? 0);
    $serviceName = trim($_POST['service_name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price       = (float) ($_POST['price'] ?? 0);
    $duration    = trim($_POST['duration'] ?? '');
    $isAvailable = isset($_POST['is_available']) ? 1 : 0;

    if (mb_strlen($serviceName) < 3) $errors['service_name'] = 'Please enter a valid service name.';
    if ($price <= 0) $errors['price'] = 'Please enter a valid price.';

    if (!$errors) {
        if ($serviceId) {
            $chk = $pdo->prepare("SELECT id FROM services WHERE id = ? AND provider_id = ?");
            $chk->execute([$serviceId, $providerId]);
            if ($chk->fetch()) {
                $upd = $pdo->prepare(
                    "UPDATE services SET service_name = ?, description = ?, price = ?, duration = ?, is_available = ?, updated_at = NOW()
                     WHERE id = ? AND provider_id = ?"
                );
                $upd->execute([$serviceName, $description, $price, $duration, $isAvailable, $serviceId, $providerId]);
                set_flash('success', 'Service updated successfully.');
            }
        } else {
            $ins = $pdo->prepare(
                "INSERT INTO services (provider_id, category_id, service_name, description, price, duration, service_image, is_available, created_at, updated_at)
                 VALUES (?, ?, ?, ?, ?, ?, '', ?, NOW(), NOW())"
            );
            $ins->execute([$providerId, $provider['category_id'], $serviceName, $description, $price, $duration, $isAvailable]);
            set_flash('success', 'Service added successfully.');
        }
    }

    if (!$errors) redirect('provider/my-services.php');
}

/* Handle delete */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    csrf_verify();
    $serviceId = (int) $_POST['service_id'];
    $del = $pdo->prepare("DELETE FROM services WHERE id = ? AND provider_id = ?");
    $del->execute([$serviceId, $providerId]);
    set_flash('success', 'Service removed.');
    redirect('provider/my-services.php');
}

$editService = null;
if (!empty($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM services WHERE id = ? AND provider_id = ?");
    $stmt->execute([(int) $_GET['edit'], $providerId]);
    $editService = $stmt->fetch();
}

$services = $pdo->prepare("SELECT * FROM services WHERE provider_id = ? ORDER BY created_at DESC");
$services->execute([$providerId]);
$services = $services->fetchAll();

$dashRole = 'provider';
$activeMenu = 'my-services';
$pageTitle = 'My Services';
$dashUserName = $provider['full_name'];
$dashUserSub = $provider['category_name'];
require __DIR__ . '/../includes/dash_shell_top.php';
?>

<div class="card">
  <div class="card-header"><h2><?php echo $editService ? 'Edit Service' : 'Add a New Service'; ?></h2></div>

  <form method="POST" action="">
    <?php echo csrf_field(); ?>
    <input type="hidden" name="action" value="save">
    <input type="hidden" name="service_id" value="<?php echo $editService ? (int) $editService['id'] : 0; ?>">

    <div class="form-row">
      <div class="form-group">
        <label>Service Name *</label>
        <input type="text" name="service_name" value="<?php echo e($editService['service_name'] ?? ''); ?>" placeholder="e.g. Fan Installation" required>
        <?php if (!empty($errors['service_name'])): ?><span class="form-error"><?php echo e($errors['service_name']); ?></span><?php endif; ?>
      </div>
      <div class="form-group">
        <label>Price (₹) *</label>
        <input type="number" step="0.01" min="1" name="price" value="<?php echo e($editService['price'] ?? ''); ?>" required>
        <?php if (!empty($errors['price'])): ?><span class="form-error"><?php echo e($errors['price']); ?></span><?php endif; ?>
      </div>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label>Duration</label>
        <input type="text" name="duration" value="<?php echo e($editService['duration'] ?? ''); ?>" placeholder="e.g. 1 Hour">
      </div>
      <div class="form-group">
        <label style="display:block;">Availability</label>
        <label style="display:flex;align-items:center;gap:8px;font-weight:500;font-size:0.9rem;margin-top:10px;">
          <input type="checkbox" name="is_available" <?php echo (!isset($editService) || $editService['is_available']) ? 'checked' : ''; ?>> Available for booking
        </label>
      </div>
    </div>

    <div class="form-group">
      <label>Description</label>
      <textarea name="description" rows="2" placeholder="Briefly describe this service"><?php echo e($editService['description'] ?? ''); ?></textarea>
    </div>

    <div style="display:flex;gap:10px;">
      <button type="submit" class="btn btn-primary"><?php echo $editService ? 'Update Service' : 'Add Service'; ?></button>
      <?php if ($editService): ?>
        <a href="<?php echo BASE_URL; ?>provider/my-services.php" class="btn btn-outline">Cancel</a>
      <?php endif; ?>
    </div>
  </form>
</div>

<div class="card">
  <div class="card-header"><h2>My Services (<?php echo count($services); ?>)</h2></div>
  <?php if (!$services): ?>
    <div class="empty-state"><div>🛠</div><p>You haven't added any services yet.</p></div>
  <?php else: ?>
    <div class="table-wrap">
      <table class="data-table">
        <thead><tr><th>Service</th><th>Description</th><th>Price</th><th>Duration</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
          <?php foreach ($services as $s): ?>
            <tr>
              <td><strong><?php echo e($s['service_name']); ?></strong></td>
              <td><?php echo e($s['description']); ?></td>
              <td><?php echo money($s['price']); ?></td>
              <td><?php echo e($s['duration'] ?: '-'); ?></td>
              <td><span class="badge badge-<?php echo $s['is_available'] ? 'active' : 'inactive'; ?>"><?php echo $s['is_available'] ? 'Available' : 'Unavailable'; ?></span></td>
              <td>
                <div class="action-btns">
                  <a class="icon-btn edit" href="?edit=<?php echo (int) $s['id']; ?>">Edit</a>
                  <form method="POST" style="display:inline;">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="service_id" value="<?php echo (int) $s['id']; ?>">
                    <button type="submit" class="icon-btn delete" data-confirm="Delete this service?">Delete</button>
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
