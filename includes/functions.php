<?php
/**
 * QuickServe - Shared helper functions
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';

/** Escape output for safe HTML rendering. */
function e($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/** Redirect helper. */
function redirect($path) {
    header('Location: ' . BASE_URL . ltrim($path, '/'));
    exit;
}

/** Format a price as Indian Rupees. */
function money($amount) {
    return '₹' . number_format((float) $amount, 2);
}

/** Format a date like 30 Jul 2026. */
function fdate($date) {
    if (!$date) return '-';
    $t = strtotime($date);
    return $t ? date('d M Y', $t) : e($date);
}

/** Format a datetime like 30 Jul 2026, 10:00 AM. */
function fdatetime($dt) {
    if (!$dt) return '-';
    $t = strtotime($dt);
    return $t ? date('d M Y, h:i A', $t) : e($dt);
}

/** CSRF token helpers. */
function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field() {
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function csrf_verify() {
    $token = $_POST['csrf_token'] ?? '';
    if (!$token || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(400);
        die('Invalid or expired form submission (CSRF check failed). Please go back and try again.');
    }
}

/**
 * Verify a password against a stored hash.
 * The seed data in the SQL dump stores customer/provider passwords as plain text
 * (e.g. 'aarav123'), while new registrations are stored as secure bcrypt hashes.
 * This helper supports both without altering the database structure, and
 * transparently upgrades a legacy plain-text password to a hash on successful login.
 */
function verify_password($plainPassword, $storedPassword, PDO $pdo = null, $table = null, $id = null) {
    if (password_get_info($storedPassword)['algo']) {
        return password_verify($plainPassword, $storedPassword);
    }
    // Legacy plain-text comparison (seed data only).
    $match = hash_equals((string) $storedPassword, (string) $plainPassword);

    if ($match && $pdo && $table && $id) {
        $newHash = password_hash($plainPassword, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("UPDATE `$table` SET password = ? WHERE id = ?");
        $stmt->execute([$newHash, $id]);
    }

    return $match;
}

/** Simple flash message system using the session. */
function set_flash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function get_flash() {
    if (!empty($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function render_flash() {
    $flash = get_flash();
    if ($flash) {
        $type = $flash['type'] === 'error' ? 'error' : $flash['type'];
        echo '<div class="alert alert-' . e($type) . '">' . e($flash['message']) . '</div>';
    }
}

/** Auth guards. */
function is_customer_logged_in() {
    return !empty($_SESSION['customer_id']);
}

function is_provider_logged_in() {
    return !empty($_SESSION['provider_id']);
}

function is_admin_logged_in() {
    return !empty($_SESSION['admin_id']);
}

function require_customer() {
    if (!is_customer_logged_in()) {
        set_flash('error', 'Please login to continue.');
        redirect('index.php?showlogin=1');
    }
}

function require_provider() {
    if (!is_provider_logged_in()) {
        set_flash('error', 'Please login as a provider to continue.');
        redirect('provider/login.php');
    }
}

function require_admin() {
    if (!is_admin_logged_in()) {
        set_flash('error', 'Please login as admin to continue.');
        redirect('admin/login.php');
    }
}

/** Resolve a category's display image (falls back to a generic icon image). */
function category_image_url($categoryName) {
    $map = [
        'Plumber'                  => 'cctv.jpg',
        'Electrician'               => 'electrician.jpg',
        'Carpenter'                 => 'carpenter.jpg',
        'Salon'                     => 'salon.jpg',
        'Home Decor'                => 'home.jpg',
        'Painter'                   => 'painter.jpg',
        'AC Services'               => 'ac.jpg',
        'Washing Machine Services'  => 'washing.jpg',
    ];
    $file = $map[$categoryName] ?? 'image.png';
    return BASE_URL . 'assets/images/categories/' . $file;
}

/** Resolve a service's image: uploaded image if present, otherwise a category-based placeholder. */
function service_image_url($service) {
    $uploaded = $service['service_image'] ?? '';
    if ($uploaded && file_exists(__DIR__ . '/../assets/images/services/' . $uploaded)) {
        return BASE_URL . 'assets/images/services/' . $uploaded;
    }
    return category_image_url($service['category_name'] ?? '');
}

/** Star rating renderer (0-5). */
function render_stars($rating) {
    $rating = round((float) $rating);
    $out = '';
    for ($i = 1; $i <= 5; $i++) {
        $out .= $i <= $rating ? '★' : '☆';
    }
    return $out;
}

/** Create a notification row for a customer and/or provider. */
function create_notification(PDO $pdo, $customerId, $providerId, $title, $message) {
    $stmt = $pdo->prepare(
        "INSERT INTO notifications (customer_id, provider_id, title, message, is_read, created_at, updated_at)
         VALUES (?, ?, ?, ?, 0, NOW(), NOW())"
    );
    $stmt->execute([$customerId, $providerId, $title, $message]);
}

/** Generate a pseudo-unique transaction id for demo payments. */
function generate_transaction_id() {
    return 'TXN' . strtoupper(bin2hex(random_bytes(5)));
}
