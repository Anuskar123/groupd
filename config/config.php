<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'freshmart_db');

// Site configuration
define('SITE_URL', 'http://localhost/freshmart/');
define('SITE_NAME', 'FreshMart Grocery Store');
define('ADMIN_EMAIL', 'admin@freshmart.com');

// Upload directories
define('UPLOAD_DIR', 'uploads/');
define('PRODUCT_IMG_DIR', UPLOAD_DIR . 'products/');

// Session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Set to 1 for HTTPS

// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Timezone
date_default_timezone_set('Asia/Kathmandu');
?>
