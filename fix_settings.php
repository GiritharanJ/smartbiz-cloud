<?php
// fix_settings.php - Run this once to create settings table
require_once 'config/db.php';

echo "<h2>🔧 SmartBiz Database Fix Tool</h2>";

try {
    $db = new Database();
    $pdo = $db->connect();
    
    // Check if settings table exists
    $result = $pdo->query("SELECT EXISTS (
        SELECT FROM information_schema.tables 
        WHERE table_name = 'settings'
    )");
    
    $tableExists = $result->fetchColumn();
    
    if (!$tableExists) {
        echo "⚠️ Settings table not found. Creating now...<br>";
        
        // Create settings table
        $sql = "CREATE TABLE settings (
            key VARCHAR(100) PRIMARY KEY,
            value TEXT,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        
        $pdo->exec($sql);
        echo "✅ Settings table created successfully!<br>";
        
        // Insert default settings
        $defaults = [
            ['business_name', 'SmartBiz'],
            ['business_logo', ''],
            ['currency', '₹'],
            ['gst_rate', '18'],
            ['gst_enabled', 'true']
        ];
        
        $stmt = $pdo->prepare("INSERT INTO settings (key, value) VALUES (?, ?)");
        foreach ($defaults as $setting) {
            $stmt->execute($setting);
        }
        
        echo "✅ Default settings inserted!<br>";
        
    } else {
        echo "✅ Settings table already exists.<br>";
        
        // Check if settings have values
        $count = $pdo->query("SELECT COUNT(*) FROM settings")->fetchColumn();
        
        if ($count == 0) {
            echo "⚠️ Settings table is empty. Inserting defaults...<br>";
            
            $defaults = [
                ['business_name', 'SmartBiz'],
                ['business_logo', ''],
                ['currency', '₹'],
                ['gst_rate', '18'],
                ['gst_enabled', 'true']
            ];
            
            $stmt = $pdo->prepare("INSERT INTO settings (key, value) VALUES (?, ?)");
            foreach ($defaults as $setting) {
                $stmt->execute($setting);
            }
            
            echo "✅ Default settings inserted!<br>";
        } else {
            echo "✅ Settings table has $count records.<br>";
        }
    }
    
    // Show current settings
    echo "<h3>Current Settings:</h3>";
    $settings = $pdo->query("SELECT * FROM settings")->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($settings) > 0) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Key</th><th>Value</th><th>Updated</th></tr>";
        foreach ($settings as $row) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['key']) . "</td>";
            echo "<td>" . htmlspecialchars($row['value']) . "</td>";
            echo "<td>" . htmlspecialchars($row['updated_at']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    echo "<br><br>";
    echo "<a href='dashboard.php' style='background: #FF7F50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Dashboard</a>";
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
    echo "Please check your database connection.";
}
?>
