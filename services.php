<?php
require_once __DIR__ . '/includes/functions.php';
require_customer();

$activePage = 'services';
$categoryFilter = trim($_GET['category'] ?? '');

$categories = $pdo->query("SELECT * FROM categories WHERE status = 1 ORDER BY id ASC")->fetchAll();

if ($categoryFilter !== '') {
    $stmt = $pdo->prepare(
        "SELECT s.*, c.category_name,
                (SELECT ROUND(AVG(r.rating), 1) FROM reviews r WHERE r.service_id = s.id) AS avg_rating,
                (SELECT COUNT(*) FROM reviews r WHERE r.service_id = s.id) AS review_count
         FROM services s
         LEFT JOIN categories c ON c.id = s.category_id
         WHERE s.is_available = 1 AND c.category_name = ?
         ORDER BY s.id ASC"
    );
    $stmt->execute([$categoryFilter]);
} else {
    $stmt = $pdo->query(
        "SELECT s.*, c.category_name,
                (SELECT ROUND(AVG(r.rating), 1) FROM reviews r WHERE r.service_id = s.id) AS avg_rating,
                (SELECT COUNT(*) FROM reviews r WHERE r.service_id = s.id) AS review_count
         FROM services s
         LEFT JOIN categories c ON c.id = s.category_id
         WHERE s.is_available = 1
         ORDER BY s.id ASC"
    );
}
$services = $stmt->fetchAll();

require __DIR__ . '/includes/public_header.php';
?>

<div class="services-page">
  <header class="page-header">
    <button type="button" class="back-button" onclick="window.location.href='<?php echo BASE_URL; ?>index.php'">← Back</button>
    <span class="label">QUICKSERVE SERVICES</span>
    <h1><?php echo $categoryFilter !== '' ? e($categoryFilter) : 'All Services'; ?></h1>
    <p>Choose a professional service for your home.</p>
  </header>

  <main class="services-page-main">

    <div class="filter-bar">
      <a href="<?php echo BASE_URL; ?>services.php" class="filter-chip <?php echo $categoryFilter === '' ? 'active' : ''; ?>">All</a>
      <?php foreach ($categories as $cat): ?>
        <a href="<?php echo BASE_URL; ?>services.php?category=<?php echo urlencode($cat['category_name']); ?>"
           class="filter-chip <?php echo $categoryFilter === $cat['category_name'] ? 'active' : ''; ?>">
          <?php echo e($cat['category_name']); ?>
        </a>
      <?php endforeach; ?>
    </div>

    <div class="services-section-heading">
      <span>AVAILABLE SERVICES</span>
      <strong><?php echo count($services); ?> Services</strong>
    </div>

    <?php if (count($services) > 0): ?>
      <div class="services-grid">
        <?php foreach ($services as $service): ?>
          <article class="service-card">
            <div class="service-card-image">
              <img src="<?php echo service_image_url($service); ?>" alt="<?php echo e($service['service_name']); ?>">
            </div>
            <div class="service-card-content">
              <h3><?php echo e($service['service_name']); ?></h3>
              <p class="service-description"><?php echo e($service['description']); ?></p>
              <div class="service-rating">
                <span class="rating-star">★</span>
                <strong><?php echo $service['avg_rating'] ? e($service['avg_rating']) : '4.8'; ?></strong>
                <span>(<?php echo (int) $service['review_count']; ?> reviews)</span>
              </div>
              <div class="service-card-bottom">
                <div class="service-price">
                  <small>Starting</small>
                  <strong><?php echo money($service['price']); ?></strong>
                </div>
                <a class="book-now-button" href="<?php echo BASE_URL; ?>customer/booking.php?service_id=<?php echo (int) $service['id']; ?>">Book Now →</a>
              </div>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="services-empty">
        <div>🔍</div>
        <h2>No services found</h2>
        <p>No services are currently available for this category.</p>
        <button type="button" onclick="window.location.href='<?php echo BASE_URL; ?>index.php'">Back to Home</button>
      </div>
    <?php endif; ?>

  </main>
</div>

<?php require __DIR__ . '/includes/public_footer.php'; ?>
