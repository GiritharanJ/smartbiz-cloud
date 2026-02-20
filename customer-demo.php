<?php
// customer-demo.php - One-click demo for any business
$type = $_GET['type'] ?? 'general';

// First, ensure they're logged in
session_start();
if (!isset($_SESSION['user_id'])) {
    // Auto-login for demo (use with caution - remove in production)
    require_once 'config/db.php';
    $db = new Database();
    $pdo = $db->connect();
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute(['admin@smartbiz.com']);
    $user = $stmt->fetch();
    
    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['role'] = $user['role'];
    }
}

// Redirect to test drive with their business type
header("Location: test_drive.php?type=$type");
exit;
?>
