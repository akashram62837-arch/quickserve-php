<?php
require_once __DIR__ . '/includes/functions.php';
$activePage = 'about';
$pageTitle = 'About Us';
require __DIR__ . '/includes/public_header.php';
?>

<div class="about-page">

  <section class="about-hero">
    <div class="about-hero-content">
      <span class="about-badge">ABOUT QUICKSERVE</span>
      <h1>Making Home Services<span> Simple, Reliable &amp; Fast</span></h1>
      <p>QuickServe is a customer-focused home service platform that connects customers with reliable professionals for everyday home service and repair needs.</p>
      <div class="about-hero-buttons">
        <a href="<?php echo BASE_URL; ?>services.php" class="about-primary-btn">Explore Our Services</a>
        <a href="<?php echo BASE_URL; ?>customer/register.php" class="about-secondary-btn">Create Account</a>
      </div>
    </div>
    <div class="about-hero-image">
      <img src="https://images.unsplash.com/photo-1605152276897-4f618f831968?auto=format&fit=crop&w=1200&q=80" alt="Professional home service">
      <div class="about-image-card">
        <div class="about-image-card-icon">✓</div>
        <div><strong>Trusted Service</strong><span>Professionals at your doorstep</span></div>
      </div>
    </div>
  </section>

  <section class="about-introduction">
    <div class="about-section-image">
      <img src="https://images.unsplash.com/photo-1681505531034-8d67054e07f6?auto=format&fit=crop&w=1200&q=80" alt="QuickServe customer service">
    </div>
    <div class="about-introduction-content">
      <span class="about-section-label">WHO WE ARE</span>
      <h2>Your Trusted Partner for Everyday Home Services</h2>
      <p>QuickServe is designed to make finding and booking home services easier for customers. From electrical work and plumbing to cleaning, appliance repair, carpentry and home improvement, customers can discover the service they need in one convenient platform.</p>
      <p>Our goal is to reduce the time and effort required to find dependable service professionals. QuickServe focuses on a simple booking experience, clear service information and a customer-friendly platform.</p>
      <div class="about-feature-list">
        <div class="about-feature-item"><span>✓</span><div><strong>Easy Booking</strong><p>Book the required home service quickly.</p></div></div>
        <div class="about-feature-item"><span>✓</span><div><strong>Reliable Professionals</strong><p>Connect with service professionals for your needs.</p></div></div>
        <div class="about-feature-item"><span>✓</span><div><strong>Customer Focused</strong><p>Designed around a smooth customer experience.</p></div></div>
      </div>
    </div>
  </section>

  <section class="about-mission-section">
    <div class="section-heading">
      <span class="about-section-label">OUR PURPOSE</span>
      <h2>What Drives QuickServe</h2>
      <p>We are building a simpler way for customers to manage their everyday home service requirements.</p>
    </div>
    <div class="about-purpose-grid">
      <div class="about-purpose-card"><div class="purpose-icon">🎯</div><h3>Our Mission</h3><p>Our mission is to make home services more accessible, convenient and organized by bringing customers and service professionals together through a simple digital platform.</p></div>
      <div class="about-purpose-card"><div class="purpose-icon">🚀</div><h3>Our Vision</h3><p>Our vision is to become a trusted platform for home services where customers can easily find the right professional and manage their service requirements with confidence.</p></div>
      <div class="about-purpose-card"><div class="purpose-icon">❤️</div><h3>Our Values</h3><p>We believe in reliability, transparency, convenience and customer satisfaction. These principles guide the way QuickServe is designed and developed.</p></div>
    </div>
  </section>

  <section class="about-why-section">
    <div class="about-why-content">
      <span class="about-section-label">WHY QUICKSERVE</span>
      <h2>Everything You Need for Your Home, in One Place</h2>
      <p>QuickServe brings different home service requirements together in one platform so customers can spend less time searching and more time getting things done.</p>
      <div class="about-why-grid">
        <div class="about-why-item"><span class="why-number">01</span><div><h3>Convenient</h3><p>Find and book services without unnecessary searching or complicated processes.</p></div></div>
        <div class="about-why-item"><span class="why-number">02</span><div><h3>Professional</h3><p>Connect customers with professionals for different home service requirements.</p></div></div>
        <div class="about-why-item"><span class="why-number">03</span><div><h3>Simple Experience</h3><p>A straightforward interface makes the service discovery and booking process easier.</p></div></div>
        <div class="about-why-item"><span class="why-number">04</span><div><h3>Built for Customers</h3><p>Every part of the platform is designed with customer convenience in mind.</p></div></div>
      </div>
    </div>
    <div class="about-why-image">
      <img src="https://images.unsplash.com/photo-1553877522-43269d4ea984?auto=format&fit=crop&w=1200&q=80" alt="QuickServe team">
    </div>
  </section>

  <section class="about-services-section">
    <div class="section-heading">
      <span class="about-section-label">OUR SERVICES</span>
      <h2>Home Services Made Easier</h2>
      <p>QuickServe brings a range of essential home services together on one convenient platform.</p>
    </div>
    <div class="about-services-grid">
      <div class="about-service-card"><div class="about-service-image"><img src="https://images.unsplash.com/photo-1534224039826-c7a0eda0e6b3?auto=format&fit=crop&w=800&q=80" alt="Electrical Services"></div><h3>Electrical Services</h3><p>Professional assistance for common electrical requirements at home.</p></div>
      <div class="about-service-card"><div class="about-service-image"><img src="https://images.unsplash.com/photo-1749532125405-70950966b0e5?auto=format&fit=crop&w=800&q=80" alt="Plumbing Services"></div><h3>Plumbing</h3><p>Convenient plumbing services for everyday household needs and repairs.</p></div>
      <div class="about-service-card"><div class="about-service-image"><img src="https://images.unsplash.com/photo-1617103996702-96ff29b1c467?auto=format&fit=crop&w=800&q=80" alt="Home Decor Services"></div><h3>Home Decor</h3><p>Home Decor designed to help keep your home comfortable and well maintained.</p></div>
      <div class="about-service-card"><div class="about-service-image"><img src="https://images.unsplash.com/photo-1765634219706-15729e7a3295?auto=format&fit=crop&w=800&q=80" alt="AC and Appliance Repair"></div><h3>AC &amp; Appliance Repair</h3><p>Get assistance with AC and household appliance service requirements.</p></div>
      <div class="about-service-card"><div class="about-service-image"><img src="https://images.unsplash.com/photo-1601058268499-e52658b8bb88?auto=format&fit=crop&w=800&q=80" alt="Carpentry Services"></div><h3>Carpentry</h3><p>Professional support for furniture, woodwork and carpentry-related requirements.</p></div>
      <div class="about-service-card"><div class="about-service-image"><img src="https://images.unsplash.com/photo-1582561424760-0321d75e81fa?auto=format&fit=crop&w=800&q=80" alt="Home Decor and Painting"></div><h3>Painting</h3><p>Home improvement services to refresh and enhance your living space.</p></div>
    </div>
  </section>

  <section class="about-customer-section">
    <div class="about-customer-image">
      <img src="https://images.unsplash.com/photo-1585569695919-db237e7cc455?auto=format&fit=crop&w=1200&q=80" alt="Customer using QuickServe service">
    </div>
    <div class="about-customer-content">
      <span class="about-section-label">CUSTOMER FIRST</span>
      <h2>Designed Around Your Convenience</h2>
      <p>We understand that home service problems can happen at any time. QuickServe aims to make the process of finding the right service as simple and convenient as possible.</p>
      <p>Whether you need an electrician, plumber, cleaner, appliance technician or another home professional, QuickServe helps you find the service you need from one platform.</p>
      <a href="<?php echo BASE_URL; ?>services.php" class="about-primary-btn">Find a Service</a>
    </div>
  </section>

  <section class="about-cta">
    <div class="about-cta-content">
      <span>GET STARTED WITH QUICKSERVE</span>
      <h2>Your Home Service Needs, Made Simple.</h2>
      <p>Discover convenient home services and connect with professionals through QuickServe.</p>
      <div class="about-cta-buttons">
        <a href="<?php echo BASE_URL; ?>services.php" class="about-cta-primary">Explore Services</a>
        <a href="<?php echo BASE_URL; ?>customer/register.php" class="about-cta-secondary">Create Your Account</a>
      </div>
    </div>
  </section>

</div>

<?php require __DIR__ . '/includes/public_footer.php'; ?>
