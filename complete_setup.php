<?php
// complete_setup.php - Creates ALL missing tables
require_once 'config/db.php';

echo "<h2>🏗️ SmartBiz Complete Database Setup</h2>";

try {
    $db = new Database();
    $pdo = $db->connect();
    
    // Array of all required tables
    $tables = [
        "users" => "
            CREATE TABLE IF NOT EXISTS users (
                id SERIAL PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                email VARCHAR(100) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL,
                role VARCHAR(50) DEFAULT 'staff',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )",
            
        "customers" => "
            CREATE TABLE IF NOT EXISTS customers (
                id SERIAL PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                phone VARCHAR(20),
                email VARCHAR(100),
                address TEXT,
                notes TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )",
            
        "products" => "
            CREATE TABLE IF NOT EXISTS products (
                id SERIAL PRIMARY KEY,
                name VARCHAR(200) NOT NULL,
                description TEXT,
                price DECIMAL(10,2) NOT NULL,
                stock INTEGER DEFAULT 0,
                supplier VARCHAR(200),
                low_stock_alert INTEGER DEFAULT 5,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )",
            
        "invoices" => "
            CREATE TABLE IF NOT EXISTS invoices (
                id SERIAL PRIMARY KEY,
                invoice_number VARCHAR(50) UNIQUE NOT NULL,
                customer_id INTEGER REFERENCES customers(id),
                total_amount DECIMAL(10,2) NOT NULL,
                gst_amount DECIMAL(10,2) DEFAULT 0,
                discount DECIMAL(10,2) DEFAULT 0,
                payment_method VARCHAR(50),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                created_by INTEGER REFERENCES users(id)
            )",
            
        "invoice_items" => "
            CREATE TABLE IF NOT EXISTS invoice_items (
                id SERIAL PRIMARY KEY,
                invoice_id INTEGER REFERENCES invoices(id) ON DELETE CASCADE,
                product_id INTEGER REFERENCES products(id),
                quantity INTEGER NOT NULL,
                price DECIMAL(10,2) NOT NULL,
                total DECIMAL(10,2) NOT NULL
            )",
            
        "expenses" => "
            CREATE TABLE IF NOT EXISTS expenses (
                id SERIAL PRIMARY KEY,
                title VARCHAR(200) NOT NULL,
                category VARCHAR(100),
                amount DECIMAL(10,2) NOT NULL,
                description TEXT,
                expense_date DATE DEFAULT CURRENT_DATE,
                created_by INTEGER REFERENCES users(id),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )",
            
        "stock_history" => "
            CREATE TABLE IF NOT EXISTS stock_history (
                id SERIAL PRIMARY KEY,
                product_id INTEGER REFERENCES products(id),
                quantity_change INTEGER NOT NULL,
                type VARCHAR(50) CHECK (type IN ('in', 'out')),
                reference_id INTEGER,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )",
            
        "settings" => "
            CREATE TABLE IF NOT EXISTS settings (
                key VARCHAR(100) PRIMARY KEY,
                value TEXT,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )"
    ];
    
    // Create each table if it doesn't exist
    foreach ($tables as $tableName => $sql) {
        try {
            $pdo->exec($sql);
            echo "✅ Table '$tableName' ready<br>";
        } catch (PDOException $e) {
            echo "❌ Error creating '$tableName': " . $e->getMessage() . "<br>";
        }
    }
    
    // Insert default settings
    $defaultSettings = [
        ['business_name', 'SmartBiz'],
        ['business_logo', ''],
        ['currency', '₹'],
        ['gst_rate', '18'],
        ['gst_enabled', 'true']
    ];
    
    foreach ($defaultSettings as $setting) {
        $stmt = $pdo->prepare("INSERT INTO settings (key, value) VALUES (?, ?) ON CONFLICT (key) DO NOTHING");
        $stmt->execute($setting);
    }
    echo "✅ Default settings inserted<br>";
    
    // Check if admin user exists
    $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE email = 'admin@smartbiz.com'");
    if ($stmt->fetchColumn() == 0) {
        $hashedPassword = password_hash('admin123', PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute(['Admin', 'admin@smartbiz.com', $hashedPassword, 'admin']);
        echo "✅ Admin user created (admin@smartbiz.com / admin123)<br>";
    } else {
        echo "✅ Admin user already exists<br>";
    }
    
    echo "<br><br>";
    echo "<div style='background: #4CAF50; color: white; padding: 15px; border-radius: 5px;'>";
    echo "<strong>✅ Setup Complete!</strong> You can now <a href='login.php' style='color: white; font-weight: bold;'>Login</a>";
    echo "</div>";
    
} catch (PDOException $e) {
    echo "<div style='background: #f44336; color: white; padding: 15px; border-radius: 5px;'>";
    echo "<strong>❌ Database Error:</strong> " . $e->getMessage();
    echo "</div>";
}
?>
