<?php
// config/db-loader.php - Auto-detects environment and loads correct DB config

// Load appropriate database class
if (isRailway()) {
    require_once __DIR__ . '/railway-db.php';

} else {
    require_once __DIR__ . '/db.php';
    // Your existing Database class
}
?>
