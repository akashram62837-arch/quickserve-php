<?php


define('DB_HOST', 'localhost');
define('DB_NAME', 'quickserve');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Base URL of the project as it will run under XAMPP.
// Change this ONLY if you rename the project folder.
define('BASE_URL', '/quickserve/');

try {
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    // Never leak credentials or full stack traces to the browser.
    die('Database connection failed. Please make sure MySQL is running in XAMPP '
        . 'and that the "quickserve_newphp" database has been imported. '
        . '(' . htmlspecialchars($e->getMessage()) . ')');
}
