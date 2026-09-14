</main>

<footer class="footer" id="contact">
  <div class="footer-container">

    <div class="footer-brand">
      <div class="footer-logo">Quick Serve</div>
      <p>Verified local pros for electrical, plumbing, washing machine, and repair — booked in under 60 seconds.</p>
    </div>

    <div class="footer-links">
      <h4>Categories</h4>
      <ul>
        <li><a href="<?php echo BASE_URL; ?>services.php?category=AC%20Services">AC Services</a></li>
        <li><a href="<?php echo BASE_URL; ?>services.php?category=Electrician">Electrician</a></li>
        <li><a href="<?php echo BASE_URL; ?>services.php?category=Plumber">Plumber</a></li>
        <li><a href="<?php echo BASE_URL; ?>services.php?category=Home%20Decor">Home Decor</a></li>
        <li><a href="<?php echo BASE_URL; ?>services.php?category=Washing%20Machine%20Services">Washing Machine</a></li>
        <li><a href="<?php echo BASE_URL; ?>services.php?category=Painter">Painter</a></li>
      </ul>
    </div>

    <div class="footer-links">
      <h4>Company</h4>
      <ul>
        <li><a href="<?php echo BASE_URL; ?>provider/register.php">Service Provider</a></li>
        <li><a href="<?php echo BASE_URL; ?>services.php">Services</a></li>
        <li><a href="<?php echo BASE_URL; ?>customer/register.php">Create Account</a></li>
        <li><a href="<?php echo BASE_URL; ?>admin/login.php">Admin Login</a></li>
        <li><a href="<?php echo BASE_URL; ?>index.php">Home</a></li>
      </ul>
    </div>

    <div class="footer-links footer-contact">
      <h4>Contact</h4>
      <ul>
        <li><a href="mailto:quickserve@gmail.com">quickserve@gmail.com</a></li>
        <li><a href="tel:+919874563210">+91 98745 63210</a></li>
        <li>Surat, Gujarat</li>
      </ul>
    </div>

  </div>

  <div class="footer-bottom">
    <p>© <?php echo date('Y'); ?> QuickServe. All rights reserved.</p>
  </div>
</footer>

<script src="<?php echo BASE_URL; ?>assets/js/main.js"></script>
<?php if (!empty($_GET['showlogin'])): ?>
<script>document.addEventListener('DOMContentLoaded', function(){ var o = document.getElementById('loginOverlay'); if (o) o.style.display = 'flex'; });</script>
<?php endif; ?>
</body>
</html>
