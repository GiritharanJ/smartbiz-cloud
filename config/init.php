<?php
// config/init.php - Load all required functions and configurations

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database connection class and helper functions
require_once __DIR__ . '/db.php';

// Helper functions (if not in db.php)
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

// Load settings (optional, can be called when needed)
function getSettings() {
    try {
        $db = new Database();
        $pdo = $db->connect();
        
        $settings = [];
        $stmt = $pdo->query("SELECT * FROM settings");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $settings[$row['key']] = $row['value'];
        }
        return $settings;
    } catch (Exception $e) {
        return [
            'business_name' => 'SmartBiz',
            'currency' => '₹',
            'gst_rate' => '18'
        ];
    }
}
?>
