<?php
require_once __DIR__ . '/../includes/functions.php';

if (is_customer_logged_in()) {
    redirect('index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $redirectTo = $_POST['redirect'] ?? '';

    if ($email === '' || $password === '') {
        set_flash('error', 'Please enter your email and password.');
        redirect('index.php?showlogin=1');
    }

    $stmt = $pdo->prepare("SELECT * FROM customers WHERE email = ?");
    $stmt->execute([$email]);
    $customer = $stmt->fetch();

    if (!$customer) {
        set_flash('error', 'No account found with this email. Please register first.');
        redirect('index.php?showlogin=1');
    }

    if ((int) $customer['status'] === 0) {
        set_flash('error', 'Your account has been disabled. Please contact support.');
        redirect('index.php?showlogin=1');
    }

    if (!verify_password($password, $customer['password'], $pdo, 'customers', $customer['id'])) {
        set_flash('error', 'Incorrect password. Please try again.');
        redirect('index.php?showlogin=1');
    }

    $_SESSION['customer_id'] = $customer['id'];
    set_flash('success', 'Welcome back, ' . $customer['full_name'] . '!');

    if ($redirectTo && strpos($redirectTo, 'quickserve') !== false) {
        header('Location: ' . $redirectTo);
        exit;
    }
    redirect('index.php');
}

redirect('index.php?showlogin=1');
