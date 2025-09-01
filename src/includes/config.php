<?php
/**
 * Student Grade Manager - Configuration File
 * 
 * This file contains all configuration settings for the application.
 * Centralizing configuration makes it easier to manage and deploy.
 */

// Application settings
define('APP_NAME', 'Student Grade Manager');
define('APP_VERSION', '1.0.0');
define('APP_ENV', 'development'); // development, production, testing

// File paths
define('ROOT_PATH', dirname(dirname(dirname(__FILE__))));
define('SRC_PATH', ROOT_PATH . '/src');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('TEMPLATES_PATH', ROOT_PATH . '/templates');
define('LOGS_PATH', ROOT_PATH . '/logs');

// URL settings
define('BASE_URL', 'http://localhost/Student/public/');
define('ASSETS_URL', BASE_URL . 'assets/');

// Grade settings
define('MIN_GRADE', 0);
define('MAX_GRADE', 100);
define('MAX_STUDENTS', 50);

// Grade thresholds
define('GRADE_A_MIN', 90);
define('GRADE_B_MIN', 80);
define('GRADE_C_MIN', 70);
define('GRADE_D_MIN', 60);

// Validation settings
define('MIN_NAME_LENGTH', 2);
define('MAX_NAME_LENGTH', 50);
define('MAX_DECIMAL_PLACES', 2);

// Error reporting
if (APP_ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Timezone
date_default_timezone_set('UTC');

// Session settings
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
if (APP_ENV === 'production') {
    ini_set('session.cookie_secure', 1);
}

// Database settings (for future use)
define('DB_HOST', 'localhost');
define('DB_NAME', 'student_grade_manager');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Logging settings
define('LOG_LEVEL', 'INFO'); // DEBUG, INFO, WARNING, ERROR
define('LOG_FILE', LOGS_PATH . '/app.log');

// Security settings
define('CSRF_TOKEN_NAME', 'csrf_token');
define('SESSION_TIMEOUT', 3600); // 1 hour

// Email settings (for future use)
define('SMTP_HOST', 'localhost');
define('SMTP_PORT', 587);
define('SMTP_USER', '');
define('SMTP_PASS', '');
define('FROM_EMAIL', 'noreply@studentgrademanager.com');
define('FROM_NAME', APP_NAME);

/**
 * Get configuration value
 * 
 * @param string $key Configuration key
 * @param mixed $default Default value if key not found
 * @return mixed Configuration value
 */
function getConfig($key, $default = null) {
    return defined($key) ? constant($key) : $default;
}

/**
 * Check if application is in development mode
 * 
 * @return bool True if in development mode
 */
function isDevelopment() {
    return APP_ENV === 'development';
}

/**
 * Check if application is in production mode
 * 
 * @return bool True if in production mode
 */
function isProduction() {
    return APP_ENV === 'production';
}

/**
 * Get asset URL
 * 
 * @param string $path Asset path relative to assets directory
 * @return string Full asset URL
 */
function assetUrl($path) {
    return ASSETS_URL . ltrim($path, '/');
}

/**
 * Get template path
 * 
 * @param string $template Template filename
 * @return string Full template path
 */
function templatePath($template) {
    return TEMPLATES_PATH . '/' . ltrim($template, '/');
}

/**
 * Log message
 * 
 * @param string $message Log message
 * @param string $level Log level
 * @return void
 */
function logMessage($message, $level = 'INFO') {
    if (!is_dir(LOGS_PATH)) {
        mkdir(LOGS_PATH, 0755, true);
    }
    
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[{$timestamp}] [{$level}] {$message}" . PHP_EOL;
    
    file_put_contents(LOG_FILE, $logEntry, FILE_APPEND | LOCK_EX);
}
?>

