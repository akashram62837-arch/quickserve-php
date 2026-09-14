<?php
require_once __DIR__ . '/../includes/functions.php';
unset($_SESSION['admin_id']);
set_flash('success', 'You have been logged out.');
redirect('admin/login.php');
