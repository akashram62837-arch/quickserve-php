<?php
require_once __DIR__ . '/../includes/functions.php';
redirect(is_customer_logged_in() ? 'customer/my-bookings.php' : 'index.php?showlogin=1');
