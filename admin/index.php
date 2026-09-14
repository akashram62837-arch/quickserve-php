<?php
require_once __DIR__ . '/../includes/functions.php';
redirect(is_admin_logged_in() ? 'admin/dashboard.php' : 'admin/login.php');
