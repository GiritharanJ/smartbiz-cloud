<?php
$pageTitle = 'Dashboard';
require_once 'config/db.php';
require_once 'components/header.php';

$db = new Database();
$pdo = $db->connect();

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

// Get sales data for chart
$stmt = $pdo->query("
    SELECT DATE(created_at) as date, SUM(total_amount) as total 
    FROM invoices 
    WHERE created_at >= CURRENT_DATE - INTERVAL '7 days'
    GROUP BY DATE(created_at) 
    ORDER BY date
");
$chartData = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <!-- Today Sales Card -->
    <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-[#713600]">
        <div class="flex items-center">
            <div class="flex-1">
                <p class="text-gray-500 text-sm">Today's Sales</p>
                <p class="text-2xl font-bold text-[#4d2c0b]">₹<?php echo number_format($todaySales, 2); ?></p>
            </div>
            <div class="bg-[#fffb8f] p-3 rounded-full">
                <i class="fas fa-rupee-sign text-[#713600] text-xl"></i>
            </div>
        </div>
    </div>
    
    <!-- Monthly Revenue Card -->
    <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-[#d16a02]">
        <div class="flex items-center">
            <div class="flex-1">
                <p class="text-gray-500 text-sm">Monthly Revenue</p>
                <p class="text-2xl font-bold text-[#4d2c0b]">₹<?php echo number_format($monthlyRevenue, 2); ?></p>
            </div>
            <div class="bg-[#fffb8f] p-3 rounded-full">
                <i class="fas fa-chart-line text-[#d16a02] text-xl"></i>
            </div>
        </div>
    </div>
    
    <!-- Total Customers Card -->
    <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-[#fcff2e]">
        <div class="flex items-center">
            <div class="flex-1">
                <p class="text-gray-500 text-sm">Total Customers</p>
                <p class="text-2xl font-bold text-[#4d2c0b]"><?php echo $totalCustomers; ?></p>
            </div>
            <div class="bg-[#fffb8f] p-3 rounded-full">
                <i class="fas fa-users text-[#fcff2e] text-xl"></i>
            </div>
        </div>
    </div>
    
    <!-- Low Stock Alert Card -->
    <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-red-500">
        <div class="flex items-center">
            <div class="flex-1">
                <p class="text-gray-500 text-sm">Low Stock Alerts</p>
                <p class="text-2xl font-bold text-[#4d2c0b]"><?php echo $lowStock; ?></p>
            </div>
            <div class="bg-red-100 p-3 rounded-full">
                <i class="fas fa-exclamation-triangle text-red-500 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Sales Chart -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-lg font-semibold text-[#4d2c0b] mb-4">Sales Overview (Last 7 Days)</h2>
        <canvas id="salesChart" height="200"></canvas>
    </div>
    
    <!-- Recent Transactions -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-lg font-semibold text-[#4d2c0b] mb-4">Recent Transactions</h2>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left text-[#4d2c0b] border-b">
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
                        <td class="py-2 text-[#713600] font-semibold">₹<?php echo number_format($transaction['total_amount'], 2); ?></td>
                        <td class="py-2"><?php echo date('d M Y', strtotime($transaction['created_at'])); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Low Stock Products -->
<div class="bg-white rounded-lg shadow-lg p-6">
    <h2 class="text-lg font-semibold text-[#4d2c0b] mb-4">Low Stock Products</h2>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="text-left text-[#4d2c0b] border-b">
                    <th class="pb-2">Product</th>
                    <th class="pb-2">Current Stock</th>
                    <th class="pb-2">Alert Level</th>
                    <th class="pb-2">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stmt = $pdo->query("SELECT * FROM products WHERE stock <= low_stock_alert LIMIT 5");
                while ($product = $stmt->fetch(PDO::FETCH_ASSOC)):
                ?>
                <tr class="border-b">
                    <td class="py-2"><?php echo $product['name']; ?></td>
                    <td class="py-2 text-red-600 font-semibold"><?php echo $product['stock']; ?></td>
                    <td class="py-2"><?php echo $product['low_stock_alert']; ?></td>
                    <td class="py-2">
                        <a href="modules/products.php?action=restock&id=<?php echo $product['id']; ?>" 
                           class="bg-[#713600] text-white px-3 py-1 rounded text-sm hover:bg-[#4d2c0b]">
                            Restock
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
// Sales Chart
const ctx = document.getElementById('salesChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: [<?php 
            foreach ($chartData as $data) {
                echo "'" . date('d M', strtotime($data['date'])) . "',";
            }
        ?>],
        datasets: [{
            label: 'Sales (₹)',
            data: [<?php 
                foreach ($chartData as $data) {
                    echo $data['total'] . ",";
                }
            ?>],
            borderColor: '#713600',
            backgroundColor: 'rgba(113, 54, 0, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '₹' + value;
                    }
                }
            }
        }
    }
});
</script>

<?php require_once 'components/footer.php'; ?>
