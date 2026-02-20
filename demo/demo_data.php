<?php
// demo/demo_data.php - FIXED VERSION with auto-clear
require_once '../config/db.php';
require_once '../config/init.php';

session_start();
$business_type = $_GET['type'] ?? 'general';

// Store in session
$_SESSION['business_type'] = $business_type;

// IMPORTANT: Set flag that demo data was loaded
$_SESSION['demo_loaded'] = true;
$_SESSION['demo_type'] = $business_type;

echo "<!DOCTYPE html>
<html>
<head>
    <title>Setting up your demo...</title>
    <meta http-equiv='refresh' content='2;url=../dashboard.php?demo=$business_type'>
    <script src='https://cdn.tailwindcss.com'></script>
    <style>
        .loader {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #FF7F50;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body class='bg-gray-50 min-h-screen flex items-center justify-center'>";

try {
    $db = new Database();
    $pdo = $db->connect();
    
    // 🔴 IMPORTANT: Clear ONLY demo-related data (keep users and settings)
    $pdo->exec("DELETE FROM invoice_items");
    $pdo->exec("DELETE FROM invoices");
    $pdo->exec("DELETE FROM products");
    $pdo->exec("DELETE FROM customers");
    $pdo->exec("DELETE FROM expenses");
    
    // Products by business type
    $products = [];
    $customers = [];
    $expenses = [];
    
    switch($business_type) {
        case 'juice':
            $products = [
                ['🍊 Fresh Orange Juice', 60, 50, 10],
                ['🍎 Apple Juice', 70, 40, 10],
                ['🍍 Mixed Fruit Juice', 80, 30, 8],
                ['🍉 Watermelon Juice', 50, 25, 5],
                ['🥭 Mango Shake', 90, 20, 5],
                ['🍋 Lemonade', 40, 60, 15],
                ['🍍 Pineapple Juice', 65, 35, 8],
                ['🍌 Banana Shake', 85, 25, 5]
            ];
            $customers = ['Rahul (Daily Regular)', 'Priya (Office)', 'Amit (Gym)', 'Neha (College)', 'Vikram (Morning Walker)'];
            $expenses = ['Fruits Purchase', 'Sugar & Ice', 'Electricity Bill', 'Staff Salary'];
            break;
            
        case 'restaurant':
            $products = [
                ['🍛 Veg Biryani', 250, 100, 15],
                ['🍗 Chicken Biryani', 350, 80, 12],
                ['🍞 Butter Naan', 40, 200, 25],
                ['🧈 Paneer Butter Masala', 300, 60, 10],
                ['🍨 Gulab Jamun', 120, 150, 20],
                ['🥤 Cold Drink', 60, 300, 30],
                ['💧 Water Bottle', 20, 500, 50]
            ];
            $customers = ['Sharma Family', 'Office Party', 'Regular Customer', 'Food Delivery', 'Tourist'];
            $expenses = ['Vegetables', 'Spices', 'Staff Salary', 'Gas Cylinder', 'Rent'];
            break;
            
        case 'salon':
            $products = [
                ['💇‍♂️ Haircut - Men', 150, 100, 15],
                ['💇‍♀️ Haircut - Women', 300, 80, 12],
                ['🪒 Shaving', 50, 150, 20],
                ['✨ Facial', 500, 30, 5],
                ['🎨 Hair Color', 800, 20, 3],
                ['💅 Manicure', 400, 25, 4],
                ['👣 Pedicure', 450, 20, 4]
            ];
            $customers = ['Raj (Weekly Regular)', 'Sneha (Bride)', 'Anil (Corporate)', 'Pooja (Student)', 'Sanjay (Monthly)'];
            $expenses = ['Shampoo', 'Oil', 'Electricity', 'Staff Salary', 'Products'];
            break;
            
        case 'petrol':
            $products = [
                ['⛽ Petrol', 102, 5000, 500],
                ['🛢️ Diesel', 94, 3000, 300],
                ['🔧 Engine Oil', 800, 50, 10],
                ['❄️ Coolant', 400, 30, 5],
                ['💧 Windshield Wash', 150, 100, 15]
            ];
            $customers = ['Taxi Driver - Raju', 'Truck Owner - Singh', 'Car Regular - Priya', 'Bike Rider - Akash', 'Fleet Customer - ABC Corp'];
            $expenses = ['Tanker Purchase', 'Electricity', 'Staff Salary', 'Maintenance'];
            break;
            
        case 'dress':
            $products = [
                ['👔 Men Shirt - S', 800, 50, 5],
                ['👔 Men Shirt - M', 800, 60, 5],
                ['👔 Men Shirt - L', 800, 55, 5],
                ['👗 Women Kurti', 1200, 40, 4],
                ['👖 Jeans - 28', 1500, 30, 3],
                ['👖 Jeans - 30', 1500, 35, 3],
                ['🧒 Kids Dress', 600, 45, 5]
            ];
            $customers = ['Family Shopper', 'College Student', 'Office Employee', 'Gift Buyer', 'Regular Customer'];
            $expenses = ['New Stock', 'Rent', 'Staff Salary', 'Electricity', 'Decoration'];
            break;
            
        case 'bakery':
            $products = [
                ['🎂 Chocolate Cake', 800, 20, 3],
                ['🧁 Pastries', 80, 200, 20],
                ['🍞 Bread', 40, 150, 15],
                ['🍪 Biscuits', 100, 100, 10],
                ['🧁 Muffins', 60, 120, 12],
                ['🍩 Donuts', 50, 150, 15]
            ];
            $customers = ['Birthday Order - Kumar', 'Regular - Priya', 'Office Order - Infosys', 'Tea Time - Sharma', 'Cake Lover - Amit'];
            $expenses = ['Flour', 'Sugar', 'Butter', 'Staff Salary', 'Electricity'];
            break;
            
        case 'factory':
            $products = [
                ['🏭 Raw Material A', 5000, 200, 20],
                ['🏭 Raw Material B', 3000, 150, 15],
                ['📦 Finished Product X', 15000, 50, 5],
                ['📦 Finished Product Y', 12000, 40, 4],
                ['🔧 Spare Part 1', 800, 300, 30]
            ];
            $customers = ['Distributor A - Patel', 'Distributor B - Shah', 'Wholesaler - Kumar', 'Retail Chain - More', 'Export Client - Singh'];
            $expenses = ['Electricity Bill', 'Staff Wages', 'Machine Maintenance', 'Raw Material', 'Transport'];
            break;
            
        default:
            $products = [
                ['🍚 Rice 5kg', 300, 100, 10],
                ['🧂 Sugar 1kg', 45, 200, 20],
                ['🫒 Oil 1L', 120, 150, 15],
                ['🧼 Soap', 30, 500, 50],
                ['🧴 Shampoo', 180, 80, 8],
                ['🪥 Toothpaste', 60, 200, 20]
            ];
            $customers = ['Home Customer', 'Shop Regular', 'Bulk Buyer', 'Neighbor', 'Passing Customer'];
            $expenses = ['Stock Purchase', 'Shop Rent', 'Staff Salary', 'Electricity'];
    }
    
    // Insert products
    $prodCount = 0;
    $prodStmt = $pdo->prepare("INSERT INTO products (name, price, stock, low_stock_alert) VALUES (?, ?, ?, ?)");
    foreach ($products as $p) {
        $prodStmt->execute([$p[0], $p[1], $p[2], $p[3]]);
        $prodCount++;
    }
    
    // Insert customers
    $custCount = 0;
    $custStmt = $pdo->prepare("INSERT INTO customers (name, phone, address) VALUES (?, ?, ?)");
    foreach ($customers as $c) {
        $phone = '98' . rand(10000000, 99999999);
        $custStmt->execute([$c, $phone, 'Local Address']);
        $custCount++;
    }
    
    // Insert expenses
    $expCount = 0;
    $expStmt = $pdo->prepare("INSERT INTO expenses (title, category, amount, description) VALUES (?, 'Operating', ?, 'Monthly expense')");
    foreach ($expenses as $e) {
        $amount = rand(1000, 8000);
        $expStmt->execute([$e, $amount]);
        $expCount++;
    }
    
    // Create sample invoices for today
    $invStmt = $pdo->prepare("INSERT INTO invoices (invoice_number, customer_id, total_amount, created_at) VALUES (?, ?, ?, NOW())");
    
    // Get customer IDs
    $custIds = $pdo->query("SELECT id FROM customers")->fetchAll(PDO::FETCH_COLUMN);
    
    for ($i = 1; $i <= 5; $i++) {
        $invNum = 'INV-' . date('Ymd') . '-' . rand(1000, 9999);
        $custId = $custIds[array_rand($custIds)];
        $total = rand(300, 3000);
        $invStmt->execute([$invNum, $custId, $total]);
    }
    
    // Success message
    echo "<div class='bg-white p-8 rounded-xl shadow-lg text-center max-w-md'>";
    echo "<div class='text-6xl mb-4'>✅</div>";
    echo "<h2 class='text-2xl font-bold mb-2'>$business_type Demo Ready!</h2>";
    echo "<p class='text-gray-600 mb-4'>Added:</p>";
    echo "<ul class='text-left space-y-1 mb-4'>";
    echo "<li>• $prodCount products for your $business_type</li>";
    echo "<li>• $custCount sample customers</li>";
    echo "<li>• " . count($expenses) . " expense categories</li>";
    echo "<li>• 5 sample invoices</li>";
    echo "</ul>";
    echo "<div class='loader'></div>";
    echo "<p class='text-sm text-gray-500 mt-4'>Redirecting to your personalized dashboard...</p>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div class='bg-red-100 p-4 rounded text-red-700'>Error: " . $e->getMessage() . "</div>";
}

echo "</body></html>";
?>
