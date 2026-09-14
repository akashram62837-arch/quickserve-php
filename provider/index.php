<?php
require_once __DIR__ . '/../includes/functions.php';
redirect(is_provider_logged_in() ? 'provider/dashboard.php' : 'provider/login.php');
