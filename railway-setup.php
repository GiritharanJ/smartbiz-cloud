<?php
// railway-setup.php - Run this ONCE on Railway
require_once 'config/db.php';

echo "<!DOCTYPE html>
<html>
<head>
    <title>Railway Database Setup</title>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <script src='https://cdn.tailwindcss.com'></script>
    <style>
        .success { color: #10b981; }
        .error { color: #ef4444; }
    </style>
</head>
<body class='bg-gray-50 p-8'>";

echo "<div class='max-w-2xl mx-auto bg-white rounded-xl shadow-lg p-6'>";
echo "<h1 class='text-2xl font-bold text-[#FF7F50] mb-4'>🚂 Railway Database Setup</h1>";

try {
    $db = new Database();
    $pdo = $db->connect();
    
    echo "<p class='text-green-600 mb-4'>✅ Connected to database successfully</p>";
    
    // Check if tables exist
    $tables = ['users', 'customers', 'products', 'invoices', 'invoice_items', 'expenses', 'settings'];
    $existing = [];
    
    foreach ($tables as $table) {
        $result = $pdo->query("SELECT EXISTS (SELECT FROM information_schema.tables WHERE table_name = '$table')");
        if ($result->fetchColumn()) {
            $existing[] = $table;
        }
    }
    
    if (count($existing) > 0) {
        echo "<p class='text-orange-600 mb-2'>⚠️ Existing tables: " . implode(', ', $existing) . "</p>";
    }
    
    // Create tables
    echo "<h2 class='text-xl font-semibold mt-6 mb-3'>Creating tables...</h2>";
    
    // Users table
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id SERIAL PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        role VARCHAR(50) DEFAULT 'staff',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "<p class='text-green-600'>✅ users table created</p>";
    
    // Customers table
    $pdo->exec("CREATE TABLE IF NOT EXISTS customers (
        id SERIAL PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        phone VARCHAR(20),
        email VARCHAR(100),
        address TEXT,
        notes TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "<p class='text-green-600'>✅ customers table created</p>";
    
    // Products table
    $pdo->exec("CREATE TABLE IF NOT EXISTS products (
        id SERIAL PRIMARY KEY,
        name VARCHAR(200) NOT NULL,
        description TEXT,
        price DECIMAL(10,2) NOT NULL,
        stock INTEGER DEFAULT 0,
        supplier VARCHAR(200),
        low_stock_alert INTEGER DEFAULT 5,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "<p class='text-green-600'>✅ products table created</p>";
    
    // Invoices table
    $pdo->exec("CREATE TABLE IF NOT EXISTS invoices (
        id SERIAL PRIMARY KEY,
        invoice_number VARCHAR(50) UNIQUE NOT NULL,
        customer_id INTEGER REFERENCES customers(id),
        total_amount DECIMAL(10,2) NOT NULL,
        gst_amount DECIMAL(10,2) DEFAULT 0,
        discount DECIMAL(10,2) DEFAULT 0,
        payment_method VARCHAR(50),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        created_by INTEGER REFERENCES users(id)
    )");
    echo "<p class='text-green-600'>✅ invoices table created</p>";
    
    // Invoice items table
    $pdo->exec("CREATE TABLE IF NOT EXISTS invoice_items (
        id SERIAL PRIMARY KEY,
        invoice_id INTEGER REFERENCES invoices(id) ON DELETE CASCADE,
        product_id INTEGER REFERENCES products(id),
        quantity INTEGER NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        total DECIMAL(10,2) NOT NULL
    )");
    echo "<p class='text-green-600'>✅ invoice_items table created</p>";
    
    // Expenses table
    $pdo->exec("CREATE TABLE IF NOT EXISTS expenses (
        id SERIAL PRIMARY KEY,
        title VARCHAR(200) NOT NULL,
        category VARCHAR(100),
        amount DECIMAL(10,2) NOT NULL,
        description TEXT,
        expense_date DATE DEFAULT CURRENT_DATE,
        created_by INTEGER REFERENCES users(id),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "<p class='text-green-600'>✅ expenses table created</p>";
    
    // Settings table
    $pdo->exec("CREATE TABLE IF NOT EXISTS settings (
        key VARCHAR(100) PRIMARY KEY,
        value TEXT,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "<p class='text-green-600'>✅ settings table created</p>";
    
    // Create admin user if not exists
    $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE email = 'admin@smartbiz.com'");
    if ($stmt->fetchColumn() == 0) {
        $hash = password_hash('admin123', PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute(['Admin', 'admin@smartbiz.com', $hash, 'admin']);
        echo "<p class='text-green-600'>✅ Admin user created</p>";
    } else {
        echo "<p class='text-blue-600'>✅ Admin user already exists</p>";
    }
    
    // Insert default settings
    $settings = [
        ['business_name', 'SmartBiz'],
        ['currency', '₹'],
        ['gst_rate', '18'],
        ['gst_enabled', 'true']
    ];
    
    foreach ($settings as $s) {
        $stmt = $pdo->prepare("INSERT INTO settings (key, value) VALUES (?, ?) ON CONFLICT (key) DO NOTHING");
        $stmt->execute([$s[0], $s[1]]);
    }
    echo "<p class='text-green-600'>✅ Default settings inserted</p>";
    
    echo "<div class='mt-8 p-4 bg-green-50 border border-green-200 rounded-lg'>";
    echo "<p class='font-bold text-green-700'>✅ Database setup complete!</p>";
    echo "<p class='text-sm mt-2'>You can now:</p>";
    echo "<ul class='list-disc pl-5 mt-2 text-sm'>";
    echo "<li><a href='login.php' class='text-[#FF7F50]'>Go to Login page</a></li>";
    echo "<li><a href='dashboard.php' class='text-[#FF7F50]'>Go to Dashboard</a></li>";
    echo "</ul>";
    echo "</div>";
    
} catch (PDOException $e) {
    echo "<div class='bg-red-50 border border-red-200 p-4 rounded-lg'>";
    echo "<p class='font-bold text-red-600'>❌ Error: " . $e->getMessage() . "</p>";
    echo "</div>";
}

echo "</div></body></html>";
?>
