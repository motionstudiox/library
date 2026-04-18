<?php
/**
 * Database Configuration
 * Update these settings with your MySQL server information
 */

define('DB_HOST', 'localhost');      // MySQL server address
define('DB_USER', 'root');           // MySQL username
define('DB_PASS', '');               // MySQL password
define('DB_NAME', 'library_db');     // Database name

// Optional: Database port (default is 3306)
define('DB_PORT', 3306);

// Display errors for development (disable in production)
define('DEBUG_MODE', true);

// Session configuration
define('SESSION_TIMEOUT', 3600);     // 1 hour in seconds
define('SITE_NAME', 'Library Management System');
define('SITE_URL', 'http://localhost/library_web');

// Current version
define('APP_VERSION', '1.0.0');
