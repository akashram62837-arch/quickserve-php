<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$stmt = $pdo->prepare("SELECT * FROM admins WHERE id = ?");
$stmt->execute([$_SESSION['admin_id']]);
$admin = $stmt->fetch();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'save') {
    csrf_verify();
    $id = (int) ($_POST['id'] ?? 0);
    $name = trim($_POST['category_name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $status = isset($_POST['status']) ? 1 : 0;

    if (mb_strlen($name) < 2) {
        $errors['category_name'] = 'Please enter a valid category name.';
    }

    if (!$errors) {
        if ($id) {
            $upd = $pdo->prepare("UPDATE categories SET category_name = ?, description = ?, status = ?, updated_at = NOW() WHERE id = ?");
            $upd->execute([$name, $description, $status, $id]);
            set_flash('success', 'Category updated.');
        } else {
            $ins = $pdo->prepare("INSERT INTO categories (category_name, description, status, image, created_at, updated_at) VALUES (?, ?, ?, '', NOW(), NOW())");
            $ins->execute([$name, $description, $status]);
            set_flash('success', 'Category added.');
        }
        redirect('admin/categories.php');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    csrf_verify();
    $id = (int) $_POST['id'];
    try {
        $pdo->prepare("DELETE FROM categories WHERE id = ?")->execute([$id]);
        set_flash('success', 'Category deleted.');
    } catch (PDOException $e) {
        set_flash('error', 'Cannot delete this category — it is linked to existing services or providers.');
    }
    redirect('admin/categories.php');
}

/* Toggle a service's availability directly from the category view */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'toggle_service') {
    csrf_verify();
    $serviceId = (int) $_POST['service_id'];
    $stmt = $pdo->prepare("SELECT is_available FROM services WHERE id = ?");
    $stmt->execute([$serviceId]);
    $current = (int) $stmt->fetchColumn();
    $pdo->prepare("UPDATE services SET is_available = ?, updated_at = NOW() WHERE id = ?")->execute([$current ? 0 : 1, $serviceId]);
    set_flash('success', 'Service availability updated.');
    redirect('admin/categories.php' . (!empty($_POST['filter']) ? '?filter=' . urlencode($_POST['filter']) : ''));
}

/* Delete a service directly from the category view */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete_service') {
    csrf_verify();
    $serviceId = (int) $_POST['service_id'];
    $pdo->prepare("DELETE FROM services WHERE id = ?")->execute([$serviceId]);
    set_flash('success', 'Service deleted.');
    redirect('admin/categories.php' . (!empty($_POST['filter']) ? '?filter=' . urlencode($_POST['filter']) : ''));
}

$editCategory = null;
if (!empty($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([(int) $_GET['edit']]);
    $editCategory = $stmt->fetch();
}

$categories = $pdo->query(
    "SELECT c.*, (SELECT COUNT(*) FROM services s WHERE s.category_id = c.id) AS service_count
     FROM categories c ORDER BY c.id ASC"
)->fetchAll();

/* Stats for the top of the page */
$totalCategories = count($categories);
$activeCategories = count(array_filter($categories, fn($c) => (int) $c['status'] === 1));
$inactiveCategories = $totalCategories - $activeCategories;
$totalServices = (int) $pdo->query("SELECT COUNT(*) FROM services")->fetchColumn();

/* All services, grouped by category, for the "Services by Category" section below */
$categoryFilter = trim($_GET['filter'] ?? '');
$allServices = $pdo->query(
    "SELECT s.*, c.category_name, p.full_name AS provider_name
     FROM services s
     LEFT JOIN categories c ON c.id = s.category_id
     LEFT JOIN providers p ON p.id = s.provider_id
     ORDER BY s.category_id ASC, s.id ASC"
)->fetchAll();

$servicesByCategory = [];
foreach ($allServices as $svc) {
    $servicesByCategory[(int) $svc['category_id']][] = $svc;
}

$dashRole = 'admin';
$activeMenu = 'categories';
$pageTitle = 'Manage Categories';
$dashUserName = $admin['full_name'];
$dashUserSub = 'Administrator';
require __DIR__ . '/../includes/dash_shell_top.php';
?>

<div class="stat-grid">
  <div class="stat-card"><div class="stat-icon">📂</div><div><strong><?php echo $totalCategories; ?></strong><span>Total Categories</span></div></div>
  <div class="stat-card"><div class="stat-icon">✅</div><div><strong><?php echo $activeCategories; ?></strong><span>Active Categories</span></div></div>
  <div class="stat-card"><div class="stat-icon">🚫</div><div><strong><?php echo $inactiveCategories; ?></strong><span>Inactive Categories</span></div></div>
  <div class="stat-card"><div class="stat-icon">🛠</div><div><strong><?php echo $totalServices; ?></strong><span>Total Services</span></div></div>
</div>

<div class="card">
  <div class="card-header"><h2><?php echo $editCategory ? 'Edit Category' : 'Add Category'; ?></h2></div>
  <form method="POST" action="">
    <?php echo csrf_field(); ?>
    <input type="hidden" name="action" value="save">
    <input type="hidden" name="id" value="<?php echo $editCategory ? (int) $editCategory['id'] : 0; ?>">
    <div class="form-row">
      <div class="form-group">
        <label>Category Name *</label>
        <input type="text" name="category_name" value="<?php echo e($editCategory['category_name'] ?? ''); ?>" required>
        <?php if (!empty($errors['category_name'])): ?><span class="form-error"><?php echo e($errors['category_name']); ?></span><?php endif; ?>
      </div>
      <div class="form-group">
        <label>Status</label>
        <label style="display:flex;align-items:center;gap:8px;font-weight:500;font-size:0.9rem;margin-top:10px;">
          <input type="checkbox" name="status" <?php echo (!isset($editCategory) || $editCategory['status']) ? 'checked' : ''; ?>> Active
        </label>
      </div>
    </div>
    <div class="form-group">
      <label>Description</label>
      <textarea name="description" rows="2"><?php echo e($editCategory['description'] ?? ''); ?></textarea>
    </div>
    <div style="display:flex;gap:10px;">
      <button type="submit" class="btn btn-primary"><?php echo $editCategory ? 'Update Category' : 'Add Category'; ?></button>
      <?php if ($editCategory): ?><a href="<?php echo BASE_URL; ?>admin/categories.php" class="btn btn-outline">Cancel</a><?php endif; ?>
    </div>
  </form>
</div>

<div class="card">
  <div class="card-header"><h2>Categories (<?php echo count($categories); ?>)</h2></div>
  <div class="table-wrap">
    <table class="data-table">
      <thead><tr><th>Name</th><th>Description</th><th>Services</th><th>Status</th><th>Actions</th></tr></thead>
      <tbody>
        <?php foreach ($categories as $cat): ?>
          <tr>
            <td><strong><?php echo e($cat['category_name']); ?></strong></td>
            <td style="white-space:normal;max-width:340px;"><?php echo e($cat['description']); ?></td>
            <td><?php echo (int) $cat['service_count']; ?></td>
            <td><span class="badge badge-<?php echo $cat['status'] ? 'active' : 'inactive'; ?>"><?php echo $cat['status'] ? 'Active' : 'Inactive'; ?></span></td>
            <td>
              <div class="action-btns">
                <a class="icon-btn edit" href="?edit=<?php echo (int) $cat['id']; ?>">Edit</a>
                <form method="POST" style="display:inline;">
                  <?php echo csrf_field(); ?>
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="id" value="<?php echo (int) $cat['id']; ?>">
                  <button type="submit" class="icon-btn delete" data-confirm="Delete this category? This cannot be undone.">Delete</button>
                </form>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<div class="card-header" style="margin-top:6px;"><h2>Services by Category</h2></div>

<div class="filter-bar">
  <a href="?filter=" class="filter-chip <?php echo $categoryFilter === '' ? 'active' : ''; ?>">All Categories</a>
  <?php foreach ($categories as $cat): ?>
    <a href="?filter=<?php echo urlencode($cat['category_name']); ?>" class="filter-chip <?php echo $categoryFilter === $cat['category_name'] ? 'active' : ''; ?>">
      <?php echo e($cat['category_name']); ?>
    </a>
  <?php endforeach; ?>
</div>

<?php foreach ($categories as $cat): ?>
  <?php
    if ($categoryFilter !== '' && $categoryFilter !== $cat['category_name']) continue;
    $catServices = $servicesByCategory[(int) $cat['id']] ?? [];
  ?>
  <div class="card">
    <div class="card-header">
      <div>
        <h2 style="margin-bottom:2px;"><?php echo e($cat['category_name']); ?></h2>
        <span style="font-size:0.82rem;color:var(--slate-500);"><?php echo count($catServices); ?> Service<?php echo count($catServices) === 1 ? '' : 's'; ?></span>
      </div>
      <span class="badge badge-<?php echo $cat['status'] ? 'active' : 'inactive'; ?>"><?php echo $cat['status'] ? 'Active' : 'Inactive'; ?></span>
    </div>

    <?php if (!$catServices): ?>
      <div class="empty-state"><div>🛠</div><p>No services have been added to this category yet.</p></div>
    <?php else: ?>
      <div class="services-grid">
        <?php foreach ($catServices as $svc): ?>
          <article class="service-card">
            <div class="service-card-image">
              <img src="<?php echo service_image_url($svc); ?>" alt="<?php echo e($svc['service_name']); ?>">
            </div>
            <div class="service-card-content">
              <h3><?php echo e($svc['service_name']); ?></h3>
              <p class="service-description"><?php echo e($svc['description']); ?></p>
              <p style="margin:0;font-size:0.78rem;color:var(--slate-500);">
                Provider: <?php echo e($svc['provider_name'] ?? 'Unassigned (catalog service)'); ?>
              </p>
              <div class="service-card-bottom">
                <div class="service-price">
                  <small>Price</small>
                  <strong><?php echo money($svc['price']); ?></strong>
                </div>
                <span class="badge badge-<?php echo $svc['is_available'] ? 'active' : 'inactive'; ?>"><?php echo $svc['is_available'] ? 'Available' : 'Unavailable'; ?></span>
              </div>
              <div class="action-btns" style="margin-top:8px;">
                <form method="POST" style="display:inline;">
                  <?php echo csrf_field(); ?>
                  <input type="hidden" name="action" value="toggle_service">
                  <input type="hidden" name="service_id" value="<?php echo (int) $svc['id']; ?>">
                  <input type="hidden" name="filter" value="<?php echo e($categoryFilter); ?>">
                  <button type="submit" class="icon-btn"><?php echo $svc['is_available'] ? 'Mark Unavailable' : 'Mark Available'; ?></button>
                </form>
                <form method="POST" style="display:inline;">
                  <?php echo csrf_field(); ?>
                  <input type="hidden" name="action" value="delete_service">
                  <input type="hidden" name="service_id" value="<?php echo (int) $svc['id']; ?>">
                  <input type="hidden" name="filter" value="<?php echo e($categoryFilter); ?>">
                  <button type="submit" class="icon-btn delete" data-confirm="Delete this service? This cannot be undone.">Delete</button>
                </form>
              </div>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
<?php endforeach; ?>

<?php require __DIR__ . '/../includes/dash_shell_bottom.php'; ?>
