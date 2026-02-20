<?php
// test_drive.php - Shows customers how it works for THEIR business
session_start();

// If not logged in, send to login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Check if tables exist
require_once 'config/db.php';
$tables_exist = true;
try {
    $db = new Database();
    $pdo = $db->connect();
    $result = $pdo->query("SELECT EXISTS (SELECT FROM information_schema.tables WHERE table_name = 'invoices')");
    if (!$result->fetchColumn()) {
        $tables_exist = false;
    }
} catch (Exception $e) {
    $tables_exist = false;
}

// Get business type from URL or session
$business_type = $_GET['type'] ?? $_SESSION['business_type'] ?? 'general';
$_SESSION['business_type'] = $business_type;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SmartBiz - See How It Works For You</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .business-card { transition: all 0.3s; }
        .business-card:hover { transform: scale(1.05); border-color: #FF7F50; }
        .step-number {
            width: 40px;
            height: 40px;
            background: #FF7F50;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 15px;
        }
    </style>
</head>
<body class="bg-gray-50">

<!-- Show database setup warning if needed -->
<?php if (!$tables_exist): ?>
<div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4 rounded">
    <div class="flex">
        <div class="flex-shrink-0">
            <i class="fas fa-exclamation-triangle text-yellow-500"></i>
        </div>
        <div class="ml-3">
            <p class="font-bold">Database not set up</p>
            <a href="railway-setup.php" class="inline-block mt-2 bg-[#FF7F50] text-white px-4 py-2 rounded-lg text-sm">
                Setup Database Now
            </a>
        </div>
    </div>
</div>
<?php endif; ?>

    <!-- Top Bar -->
    <div class="bg-white shadow-sm">
        <div class="max-w-6xl mx-auto px-4 py-3 flex justify-between items-center">
            <div class="flex items-center">
                <i class="fas fa-store text-2xl text-[#FF7F50] mr-2"></i>
                <span class="font-bold text-xl">SmartBiz</span>
            </div>
            <div>
                <span class="text-sm text-gray-500 mr-3">Welcome, <?php echo $_SESSION['user_name'] ?? 'Demo User'; ?></span>
                <a href="logout.php" class="text-sm text-red-500">Logout</a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-4xl mx-auto p-4">
        
        <!-- Step 1: Choose Business Type -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
            <h2 class="text-xl font-bold mb-4">👋 Tell us about your business</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <a href="?type=juice" class="business-card p-4 border-2 <?php echo $business_type=='juice' ? 'border-[#FF7F50] bg-orange-50' : 'border-gray-200'; ?> rounded-xl text-center">
                    <i class="fas fa-glass-cheers text-2xl text-[#FF7F50] mb-2"></i>
                    <div class="font-medium">Juice Shop</div>
                </a>
                <a href="?type=restaurant" class="business-card p-4 border-2 <?php echo $business_type=='restaurant' ? 'border-[#FF7F50] bg-orange-50' : 'border-gray-200'; ?> rounded-xl text-center">
                    <i class="fas fa-utensils text-2xl text-[#FF7F50] mb-2"></i>
                    <div class="font-medium">Restaurant</div>
                </a>
                <a href="?type=salon" class="business-card p-4 border-2 <?php echo $business_type=='salon' ? 'border-[#FF7F50] bg-orange-50' : 'border-gray-200'; ?> rounded-xl text-center">
                    <i class="fas fa-cut text-2xl text-[#FF7F50] mb-2"></i>
                    <div class="font-medium">Salon</div>
                </a>
                <a href="?type=petrol" class="business-card p-4 border-2 <?php echo $business_type=='petrol' ? 'border-[#FF7F50] bg-orange-50' : 'border-gray-200'; ?> rounded-xl text-center">
                    <i class="fas fa-gas-pump text-2xl text-[#FF7F50] mb-2"></i>
                    <div class="font-medium">Petrol Bunk</div>
                </a>
                <a href="?type=dress" class="business-card p-4 border-2 <?php echo $business_type=='dress' ? 'border-[#FF7F50] bg-orange-50' : 'border-gray-200'; ?> rounded-xl text-center">
                    <i class="fas fa-tshirt text-2xl text-[#FF7F50] mb-2"></i>
                    <div class="font-medium">Dress Shop</div>
                </a>
                <a href="?type=bakery" class="business-card p-4 border-2 <?php echo $business_type=='bakery' ? 'border-[#FF7F50] bg-orange-50' : 'border-gray-200'; ?> rounded-xl text-center">
                    <i class="fas fa-cake-candles text-2xl text-[#FF7F50] mb-2"></i>
                    <div class="font-medium">Bakery</div>
                </a>
                <a href="?type=factory" class="business-card p-4 border-2 <?php echo $business_type=='factory' ? 'border-[#FF7F50] bg-orange-50' : 'border-gray-200'; ?> rounded-xl text-center">
                    <i class="fas fa-industry text-2xl text-[#FF7F50] mb-2"></i>
                    <div class="font-medium">Small Factory</div>
                </a>
                <a href="?type=general" class="business-card p-4 border-2 <?php echo $business_type=='general' ? 'border-[#FF7F50] bg-orange-50' : 'border-gray-200'; ?> rounded-xl text-center">
                    <i class="fas fa-store text-2xl text-[#FF7F50] mb-2"></i>
                    <div class="font-medium">General Store</div>
                </a>
            </div>
        </div>

        <?php
        // Show business-specific guide
        $guide_title = "";
        $guide_steps = [];
        $guide_features = [];
        
        switch($business_type) {
            case 'juice':
                $guide_title = "🧃 How SmartBiz Helps Your Juice Shop";
                $guide_steps = [
                    "Add your juices (Mango, Orange, Apple) with prices",
                    "Track fruit inventory - get alert when stock is low",
                    "Create bills in 30 seconds for customers",
                    "See which juice sells the most in reports",
                    "Track daily collection and profit"
                ];
                $guide_features = [
                    "Fast billing for rush hours",
                    "Fruit wastage tracking",
                    "Popular juice reports",
                    "Expiry alerts for fresh juices"
                ];
                break;
                
            case 'restaurant':
                $guide_title = "🍽️ How SmartBiz Helps Your Restaurant";
                $guide_steps = [
                    "Add your menu items with prices",
                    "Create bills for table orders",
                    "Split bills for large parties",
                    "Track best-selling dishes",
                    "Monitor daily collections"
                ];
                $guide_features = [
                    "Table management",
                    "Kitchen order printing",
                    "Popular items report",
                    "GST calculation included"
                ];
                break;
                
            case 'salon':
                $guide_title = "✂️ How SmartBiz Helps Your Salon";
                $guide_steps = [
                    "Add your services (Haircut, Facial, Manicure)",
                    "Save regular customer details",
                    "Track customer visit history",
                    "Create bills with multiple services",
                    "See monthly revenue"
                ];
                $guide_features = [
                    "Customer loyalty tracking",
                    "Service-wise profit report",
                    "Appointment reminders",
                    "Staff performance tracking"
                ];
                break;
                
            case 'petrol':
                $guide_title = "⛽ How SmartBiz Helps Your Petrol Bunk";
                $guide_steps = [
                    "Set fuel rates (Petrol ₹102, Diesel ₹94)",
                    "Track fuel stock in liters",
                    "Record vehicle-wise sales",
                    "Get low stock alerts",
                    "View daily fuel sales report"
                ];
                $guide_features = [
                    "Fuel stock monitoring",
                    "Vehicle-wise tracking",
                    "Automatic rate updates",
                    "Shift-wise collection"
                ];
                break;
                
            case 'dress':
                $guide_title = "👕 How SmartBiz Helps Your Dress Shop";
                $guide_steps = [
                    "Add products with sizes (S, M, L, XL)",
                    "Track inventory by size and color",
                    "Manage seasonal collections",
                    "Create bills with discounts",
                    "See which sizes sell most"
                ];
                $guide_features = [
                    "Size-wise inventory",
                    "Color tracking",
                    "Seasonal sale reports",
                    "Customer preferences"
                ];
                break;
                
            case 'bakery':
                $guide_title = "🎂 How SmartBiz Helps Your Bakery";
                $guide_steps = [
                    "Add cakes, pastries, and bread items",
                    "Set expiry alerts for fresh items",
                    "Manage birthday cake orders",
                    "Track best-selling items",
                    "Daily collection report"
                ];
                $guide_features = [
                    "Freshness tracking",
                    "Cake order management",
                    "Expiry alerts",
                    "Popular item reports"
                ];
                break;
                
            case 'factory':
                $guide_title = "🏭 How SmartBiz Helps Your Small Factory";
                $guide_steps = [
                    "Add raw materials inventory",
                    "Add finished products",
                    "Track raw material used in production",
                    "Record sales to distributors",
                    "Calculate profit automatically"
                ];
                $guide_features = [
                    "Raw material tracking",
                    "Production costing",
                    "Distributor management",
                    "Profit calculation"
                ];
                break;
                
            default:
                $guide_title = "🏪 How SmartBiz Helps Your General Store";
                $guide_steps = [
                    "Add all your products with prices",
                    "Track stock levels automatically",
                    "Create bills for customers",
                    "Manage customer accounts",
                    "See daily sales reports"
                ];
                $guide_features = [
                    "Easy billing",
                    "Low stock alerts",
                    "Customer credit tracking",
                    "Sales reports"
                ];
        }
        ?>

        <!-- Step 2: Show Guide -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
            <h2 class="text-2xl font-bold text-[#FF7F50] mb-4"><?php echo $guide_title; ?></h2>
            
            <div class="grid md:grid-cols-2 gap-6">
                <!-- Steps -->
                <div>
                    <h3 class="font-semibold text-lg mb-3">📋 Simple 5-Step Setup</h3>
                    <?php foreach ($guide_steps as $index => $step): ?>
                    <div class="flex items-start mb-3">
                        <div class="step-number"><?php echo $index + 1; ?></div>
                        <div><?php echo $step; ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Features -->
                <div class="bg-orange-50 p-4 rounded-xl">
                    <h3 class="font-semibold text-lg mb-3">✨ Key Features For You</h3>
                    <ul class="space-y-2">
                        <?php foreach ($guide_features as $feature): ?>
                        <li class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                            <?php echo $feature; ?>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex gap-3 mt-6 pt-4 border-t">
                <a href="demo/demo_data.php?type=<?php echo $business_type; ?>" 
                   class="bg-[#FF7F50] text-white px-6 py-3 rounded-lg font-semibold hover:bg-[#FF4500]">
                    <i class="fas fa-magic mr-2"></i>
                    Load Sample <?php echo ucfirst($business_type); ?> Data
                </a>
                <a href="dashboard.php?demo=<?php echo $business_type; ?>" 
                   class="border-2 border-[#FF7F50] text-[#FF7F50] px-6 py-3 rounded-lg font-semibold hover:bg-[#FF7F50] hover:text-white">
                    Go to Dashboard →
                </a>
            </div>
            
            <p class="text-sm text-gray-500 mt-3">
                <i class="fas fa-info-circle mr-1"></i>
                Click "Load Sample Data" to auto-fill your dashboard with <?php echo ucfirst($business_type); ?> products
            </p>
        </div>
        
        <!-- Testimonial -->
        <div class="bg-blue-50 p-4 rounded-lg text-sm">
            <i class="fas fa-quote-left text-blue-500 mr-1"></i>
            <span>I run a <?php echo $business_type; ?> business. SmartBiz saved me 2 hours every day on billing!</span>
            <div class="mt-2 text-blue-600 font-medium">- Happy Customer</div>
        </div>
    </div>
</body>
</html>
