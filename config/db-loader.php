<?php
// config/db-loader.php - Auto-detects environment and loads correct DB config

// Detect if running on Railway
function isRailway() {
    return getenv('RAILWAY_SERVICE_NAME') !== false || 
           getenv('RAILWAY_ENVIRONMENT') !== false ||
           file_exists('/etc/railway'); // Railway detection
}

// Load appropriate database class
if (isRailway()) {
    require_once __DIR__ . '/railway-db.php';
    class Database extends RailwayDatabase {
        // RailwayDatabase already has connect() method
    }
} else {
    require_once __DIR__ . '/db.php';
    // Your existing Database class
}

// Test function (optional)
function testDatabaseConnection() {
    try {
        $db = new Database();
        $conn = $db->connect();
        echo "✅ Database connected successfully on " . (isRailway() ? "Railway" : "Local") . "<br>";
        return true;
    } catch (Exception $e) {
        echo "❌ Connection failed: " . $e->getMessage() . "<br>";
        return false;
    }
}
?>
