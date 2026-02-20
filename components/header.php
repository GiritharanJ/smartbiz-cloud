  <?php
// Add this line at the VERY TOP of header.php
  require_once __DIR__ . '/../config/database.php';
  require_once __DIR__ . '/../config/db.php';

// Now this function will be available
if (!isLoggedIn()) {
    redirect('login.php');
}

// Get business settings with error handling
$db = new Database();
$pdo = $db->connect();
$settings = [];

// Check if settings table exists before querying
try {
    $tableCheck = $pdo->query("SELECT EXISTS (
        SELECT FROM information_schema.tables 
        WHERE table_name = 'settings'
    )");
    
    if ($tableCheck->fetchColumn()) {
        $stmt = $pdo->query("SELECT * FROM settings");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $settings[$row['key']] = $row['value'];
        }
    } else {
        // Default settings if table doesn't exist
        $settings = [
            'business_name' => 'SmartBiz',
            'business_logo' => '',
            'currency' => '₹',
            'gst_rate' => '18',
            'gst_enabled' => 'true'
        ];
    }
} catch (PDOException $e) {
    // Fallback to defaults on error
    $settings = [
        'business_name' => 'SmartBiz',
        'business_logo' => '',
        'currency' => '₹',
        'gst_rate' => '18',
        'gst_enabled' => 'true'
    ];
    
    // Log error for debugging
    error_log("Settings table error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartBiz - <?php echo $pageTitle ?? 'Dashboard'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .bg-primary { background-color: #FF7F50; }
        .text-primary { color: #FF7F50; }
        .border-primary { border-color: #FF7F50; }
        .hover\:bg-primary:hover { background-color: #FF4500; }
        .sidebar { background-color: #FF7F50; }
        .card { box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Rest of your header code... -->
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="sidebar w-64 text-white">
            <div class="p-4">
                <h2 class="text-2xl font-bold mb-6"><?php echo $settings['business_name'] ?? 'SmartBiz'; ?></h2>
                <nav>
                    <a href="/dashboard.php" class="flex items-center py-2 px-4 hover:bg-[#4d2c0b] rounded mb-1">
                        <i class="fas fa-dashboard w-6"></i> Dashboard
                    </a>
                    <a href="/modules/invoices.php" class="flex items-center py-2 px-4 hover:bg-[#4d2c0b] rounded mb-1">
                        <i class="fas fa-file-invoice w-6"></i> Billing
                    </a>
                    <a href="/modules/customers.php" class="flex items-center py-2 px-4 hover:bg-[#4d2c0b] rounded mb-1">
                        <i class="fas fa-users w-6"></i> Customers
                    </a>
                    <a href="/modules/products.php" class="flex items-center py-2 px-4 hover:bg-[#4d2c0b] rounded mb-1">
                        <i class="fas fa-box w-6"></i> Products
                    </a>
                    <a href="/modules/expenses.php" class="flex items-center py-2 px-4 hover:bg-[#4d2c0b] rounded mb-1">
                        <i class="fas fa-money-bill w-6"></i> Expenses
                    </a>
                    <a href="/modules/reports.php" class="flex items-center py-2 px-4 hover:bg-[#4d2c0b] rounded mb-1">
                        <i class="fas fa-chart-line w-6"></i> Reports
                    </a>
                    <?php if (isAdmin()): ?>
                    <a href="/modules/settings.php" class="flex items-center py-2 px-4 hover:bg-[#4d2c0b] rounded mb-1">
                        <i class="fas fa-cog w-6"></i> Settings
                    </a>
                    <?php endif; ?>
                </nav>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Header -->
            <header class="bg-white shadow-sm p-4 flex justify-between items-center">
                <h1 class="text-xl font-semibold text-[#4d2c0b]"><?php echo $pageTitle ?? 'Dashboard'; ?></h1>
                <div class="flex items-center">
                    <span class="mr-4 text-[#4d2c0b]">Welcome, <?php echo $_SESSION['user_name']; ?></span>
                    <a href="logout.php" class="bg-[#713600] text-white px-4 py-2 rounded hover:bg-[#4d2c0b]">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </header>
            
            <!-- Content Area -->
            <main class="flex-1 overflow-y-auto p-6">
