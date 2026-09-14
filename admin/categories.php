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

$dashRole = 'admin';
$activeMenu = 'categories';
$pageTitle = 'Manage Categories';
$dashUserName = $admin['full_name'];
$dashUserSub = 'Administrator';
require __DIR__ . '/../includes/dash_shell_top.php';
?>

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

<?php require __DIR__ . '/../includes/dash_shell_bottom.php'; ?>
