<?php
$pageTitle = 'Dashboard';
require_once 'config/init.php';
require_once 'components/header.php';

$db = new Database();
$pdo = $db->connect();

// Show welcome message based on demo type
$demoType = $_SESSION['demo_type'] ?? 'general';
$demoJustLoaded = $_GET['demo'] ?? '';

if ($demoJustLoaded) {
    echo "<div class='bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded shadow'>";
    echo "<div class='flex'>";
    echo "<div class='flex-shrink-0'><i class='fas fa-check-circle text-green-500 text-xl'></i></div>";
    echo "<div class='ml-3'>";
    echo "<p class='font-bold'>✨ Your " . ucfirst($demoJustLoaded) . " Demo is Ready!</p>";
    echo "<p class='text-sm'>We've loaded sample products and customers for your " . ucfirst($demoJustLoaded) . " business. Try creating an invoice!</p>";
    echo "</div></div></div>";
}

// Get today's sales
$stmt = $pdo->query("SELECT COALESCE(SUM(total_amount), 0) as total FROM invoices WHERE DATE(created_at) = CURRENT_DATE");
$todaySales = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Get monthly revenue
$stmt = $pdo->query("SELECT COALESCE(SUM(total_amount), 0) as total FROM invoices WHERE EXTRACT(MONTH FROM created_at) = EXTRACT(MONTH FROM CURRENT_DATE)");
$monthlyRevenue = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Get total customers
$stmt = $pdo->query("SELECT COUNT(*) as total FROM customers");
$totalCustomers = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Get low stock products
$stmt = $pdo->query("SELECT COUNT(*) as total FROM products WHERE stock <= low_stock_alert");
$lowStock = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Get recent transactions
$stmt = $pdo->query("
    SELECT i.*, c.name as customer_name 
    FROM invoices i 
    LEFT JOIN customers c ON i.customer_id = c.id 
    ORDER BY i.created_at DESC LIMIT 5
");
$recentTransactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get low stock products list
$stmt = $pdo->query("SELECT * FROM products WHERE stock <= low_stock_alert ORDER BY stock ASC LIMIT 5");
$lowStockProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Success Message if demo just loaded -->
<?php if ($demoJustLoaded): ?>
<script>
    // Show popup or notification
    setTimeout(function() {
        alert('🎉 Your <?php echo ucfirst($demoJustLoaded); ?> dashboard is ready!\n\nTry these features:\n1. Go to Billing → Create Invoice\n2. Check Products → See your items\n3. View Reports → See sales');
    }, 500);
</script>
<?php endif; ?>

<!-- Dashboard Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <!-- Today Sales Card -->
    <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-[#FF7F50]">
        <div class="flex items-center">
            <div class="flex-1">
                <p class="text-gray-500 text-sm">Today's Sales</p>
                <p class="text-2xl font-bold text-gray-800">₹<?php echo number_format($todaySales, 2); ?></p>
            </div>
            <div class="bg-orange-100 p-3 rounded-full">
                <i class="fas fa-rupee-sign text-[#FF7F50] text-xl"></i>
            </div>
        </div>
    </div>
    
    <!-- Monthly Revenue Card -->
    <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-[#FF7F50]">
        <div class="flex items-center">
            <div class="flex-1">
                <p class="text-gray-500 text-sm">Monthly Revenue</p>
                <p class="text-2xl font-bold text-gray-800">₹<?php echo number_format($monthlyRevenue, 2); ?></p>
            </div>
            <div class="bg-orange-100 p-3 rounded-full">
                <i class="fas fa-chart-line text-[#FF7F50] text-xl"></i>
            </div>
        </div>
    </div>
    
    <!-- Total Customers Card -->
    <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-[#FF7F50]">
        <div class="flex items-center">
            <div class="flex-1">
                <p class="text-gray-500 text-sm">Total Customers</p>
                <p class="text-2xl font-bold text-gray-800"><?php echo $totalCustomers; ?></p>
            </div>
            <div class="bg-orange-100 p-3 rounded-full">
                <i class="fas fa-users text-[#FF7F50] text-xl"></i>
            </div>
        </div>
    </div>
    
    <!-- Low Stock Alert Card -->
    <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-red-500">
        <div class="flex items-center">
            <div class="flex-1">
                <p class="text-gray-500 text-sm">Low Stock Alerts</p>
                <p class="text-2xl font-bold text-gray-800"><?php echo $lowStock; ?></p>
            </div>
            <div class="bg-red-100 p-3 rounded-full">
                <i class="fas fa-exclamation-triangle text-red-500 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Business Type Quick Links -->
<div class="bg-white rounded-lg shadow-lg p-4 mb-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center">
            <i class="fas fa-store text-[#FF7F50] mr-2"></i>
            <span class="font-medium">Your Business: </span>
            <span class="ml-2 bg-[#FF7F50] text-white px-3 py-1 rounded-full text-sm">
                <?php echo ucfirst($demoType); ?> Shop
            </span>
        </div>
        <div class="text-sm text-gray-500">
            <i class="fas fa-info-circle mr-1"></i>
            Data loaded for <?php echo ucfirst($demoType); ?> business
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Recent Transactions -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Recent Transactions</h2>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left text-gray-600 border-b">
                        <th class="pb-2">Invoice #</th>
                        <th class="pb-2">Customer</th>
                        <th class="pb-2">Amount</th>
                        <th class="pb-2">Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentTransactions as $transaction): ?>
                    <tr class="border-b">
                        <td class="py-2"><?php echo $transaction['invoice_number']; ?></td>
                        <td class="py-2"><?php echo $transaction['customer_name'] ?? 'Walk-in'; ?></td>
                        <td class="py-2 text-[#FF7F50] font-semibold">₹<?php echo number_format($transaction['total_amount'], 2); ?></td>
                        <td class="py-2"><?php echo date('d M', strtotime($transaction['created_at'])); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Low Stock Products -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Low Stock Products</h2>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left text-gray-600 border-b">
                        <th class="pb-2">Product</th>
                        <th class="pb-2">Current Stock</th>
                        <th class="pb-2">Alert Level</th>
                        <th class="pb-2">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($lowStockProducts as $product): ?>
                    <tr class="border-b">
                        <td class="py-2"><?php echo $product['name']; ?></td>
                        <td class="py-2 text-red-600 font-semibold"><?php echo $product['stock']; ?></td>
                        <td class="py-2"><?php echo $product['low_stock_alert']; ?></td>
                        <td class="py-2">
                            <a href="modules/products.php?action=restock&id=<?php echo $product['id']; ?>" 
                               class="bg-[#FF7F50] text-white px-3 py-1 rounded text-sm hover:bg-[#FF4500]">
                                Restock
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Quick Actions based on business type -->
<div class="bg-white rounded-lg shadow-lg p-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions for Your <?php echo ucfirst($demoType); ?> Business</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <a href="modules/invoices.php" class="p-4 border rounded-lg text-center hover:bg-orange-50">
            <i class="fas fa-file-invoice text-2xl text-[#FF7F50] mb-2"></i>
            <div>Create Invoice</div>
        </a>
        <a href="modules/products.php" class="p-4 border rounded-lg text-center hover:bg-orange-50">
            <i class="fas fa-boxes text-2xl text-[#FF7F50] mb-2"></i>
            <div>Manage Products</div>
        </a>
        <a href="modules/customers.php" class="p-4 border rounded-lg text-center hover:bg-orange-50">
            <i class="fas fa-users text-2xl text-[#FF7F50] mb-2"></i>
            <div>View Customers</div>
        </a>
        <a href="modules/reports.php" class="p-4 border rounded-lg text-center hover:bg-orange-50">
            <i class="fas fa-chart-bar text-2xl text-[#FF7F50] mb-2"></i>
            <div>View Reports</div>
        </a>
    </div>
</div>

<?php require_once 'components/footer.php'; ?>
