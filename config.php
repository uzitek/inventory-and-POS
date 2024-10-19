<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'inventory_pos');

// Establish database connection
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set default timezone
date_default_timezone_set('UTC');

// Company information
define('COMPANY_NAME', 'PackagingCountry Shop');
define('COMPANY_ADDRESS', 'i135, Mammy Market, Ojo Army Cantonment, Ojo Barracks');
define('COMPANY_EMAIL', 'packagingcountry@gmail.com');
define('COMPANY_PHONE', '07069771903');
define('DESIGNER', 'HAsa Solutions');
define('DESIGNER_EMAIL', 'hasa@cyberservices.com');
define('DESIGNER_PHONE', '08066730594');
define('COMPANY_LOGO', 'path/to/your/logo.png');

// User roles
define('ROLE_ADMIN', 1);
define('ROLE_STAFF', 2);

// Error logging
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

// CSRF Protection
session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>