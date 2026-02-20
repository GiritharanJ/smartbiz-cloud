<?php
// config/database.php - Unified database loader

// Prevent multiple inclusions
if (!defined('DATABASE_LOADED')) {
    define('DATABASE_LOADED', true);
    
    // Detect environment
    $isRailway = getenv('RAILWAY_SERVICE_NAME') !== false;
    
    if ($isRailway) {
        // Load Railway-specific configuration
        require_once __DIR__ . '/railway-db.php';
    } else {
        // Load local configuration
        require_once __DIR__ . '/db.php';
    }
    
    // Start session
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Helper functions
    if (!function_exists('isLoggedIn')) {
        function isLoggedIn() {
            return isset($_SESSION['user_id']);
        }
    }
    
    if (!function_exists('isAdmin')) {
        function isAdmin() {
            return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
        }
    }
    
    if (!function_exists('redirect')) {
        function redirect($url) {
            header("Location: $url");
            exit();
        }
    }
}
?>
