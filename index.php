<?php
require_once __DIR__ . '/includes/functions.php';

$activePage = 'home';
$pageTitle = null; // use default site title

$categories = $pdo->query("SELECT * FROM categories WHERE status = 1 ORDER BY id ASC")->fetchAll();

require __DIR__ . '/includes/public_header.php';
?>

<div class="landing-page">

  <section class="qs-main-hero">
    <div class="hero-left">
      <span class="hero-label">QUICK SERVE</span>
      <h1>Home services<br><span>at your doorstep</span></h1>
      <p>Professional and reliable home services from verified experts in your city.</p>
    </div>
    <div class="hero-image-container">
      <img src="<?php echo BASE_URL; ?>assets/images/hero-services.jpg" alt="QuickServe Home Services" class="hero-services-image">
    </div>
  </section>

  <section class="quick-category-section">
    <div class="section-heading"><h2>What are you looking for?</h2></div>
    <div class="quick-category-grid">
      <?php foreach ($categories as $cat): ?>
        <a href="<?php echo BASE_URL; ?>services.php?category=<?php echo urlencode($cat['category_name']); ?>" class="quick-category-card">
          <div class="quick-category-image">
            <img src="<?php echo category_image_url($cat['category_name']); ?>" alt="<?php echo e($cat['category_name']); ?>">
          </div>
          <strong><?php echo e($cat['category_name']); ?></strong>
        </a>
      <?php endforeach; ?>
    </div>
  </section>

  <section class="spotlight-section">
    <div class="spotlight-grid">
      <div class="spotlight-item spotlight-one">
        <img src="https://images.unsplash.com/photo-1553877522-43269d4ea984?auto=format&fit=crop&w=1200&q=80" alt="Business service" class="spotlight-image">
        <div class="spotlight-overlay">
          <h3>Shine your business deserves</h3>
          <button type="button" onclick="window.location.href='<?php echo BASE_URL; ?>services.php'">Book now</button>
        </div>
      </div>
      <div class="spotlight-item spotlight-two">
        <img src="https://images.unsplash.com/photo-1596902852627-406f07653cd9?auto=format&fit=crop&w=1200&q=80" alt="Washing service" class="spotlight-image">
        <div class="spotlight-overlay">
          <h3>Washing really makes a difference</h3>
          <button type="button" onclick="window.location.href='<?php echo BASE_URL; ?>services.php'">Book now</button>
        </div>
      </div>
      <div class="spotlight-item spotlight-three">
        <img src="https://images.unsplash.com/photo-1570174006382-148305ce4972?auto=format&fit=crop&w=1200&q=80" alt="Home service" class="spotlight-image">
        <div class="spotlight-overlay">
          <h3>Relax &amp; rejuvenate at home</h3>
          <button type="button" onclick="window.location.href='<?php echo BASE_URL; ?>services.php'">Book now</button>
        </div>
      </div>
    </div>
  </section>

  <section class="large-promotion">
    <img src="https://images.unsplash.com/photo-1640357897497-599b4fc84f51?auto=format&fit=crop&w=1600&q=80" alt="Smart home services" class="large-promotion-image">
    <div class="promotion-overlay">
      <small>QUICKSERVE SPECIAL</small>
      <h2>Smart home services.<br>Made simple.</h2>
      <p>Get trusted professionals for all your home maintenance needs.</p>
      <button type="button" onclick="window.location.href='<?php echo BASE_URL; ?>services.php'">Book now</button>
    </div>
  </section>

  <section class="trust-section">
    <div class="trust-item"><span class="trust-icon">👥</span><div><strong>15,000+</strong><span>Services completed</span></div></div>
    <div class="trust-item"><span class="trust-icon">🛡️</span><div><strong>500+</strong><span>Verified professionals</span></div></div>
    <div class="trust-item"><span class="trust-icon">⭐</span><div><strong>4.9/5</strong><span>Customer rating</span></div></div>
    <div class="trust-item"><span class="trust-icon">⏱️</span><div><strong>30 min</strong><span>Average response</span></div></div>
  </section>

</div>

<?php require __DIR__ . '/includes/public_footer.php'; ?>
