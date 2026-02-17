<?php
session_start();
session_regenerate_id(true); // security improvement

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SmartBiz – Business Billing & Inventory Software</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="SmartBiz is a billing and inventory management software for small businesses like dress shops, restaurants, and juice shops.">
<meta name="keywords" content="billing software India, GST billing software, dress shop billing system, small business inventory software">
<meta name="author" content="SmartBiz">

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        /* Custom color classes using only your specified colors */
        .primary-dark { color: #AD4A28; }
        .primary-medium { color: #DD723C; }
        .primary-bright { color: #FC7001; }
        
        .bg-primary-dark { background-color: #AD4A28; }
        .bg-primary-medium { background-color: #DD723C; }
        .bg-primary-bright { background-color: #FC7001; }
        
        .border-primary-dark { border-color: #AD4A28; }
        .border-primary-medium { border-color: #DD723C; }
        .border-primary-bright { border-color: #FC7001; }
        
        .hover\:bg-primary-dark:hover { background-color: #AD4A28; }
        .hover\:bg-primary-medium:hover { background-color: #DD723C; }
        .hover\:bg-primary-bright:hover { background-color: #FC7001; }
        
        .hover\:text-primary-dark:hover { color: #AD4A28; }
        .hover\:text-primary-medium:hover { color: #DD723C; }
        .hover\:text-primary-bright:hover { color: #FC7001; }
        
        /* Gradient using only your colors */
        .gradient-custom {
            background: linear-gradient(135deg, #FC7001 0%, #DD723C 70%, #AD4A28 100%);
        }
        
        /* Card hover effect */
        .feature-card {
            transition: all 0.3s ease;
            border: 1px solid transparent;
        }
        .feature-card:hover {
            border-color: #FC7001;
            box-shadow: 0 10px 30px -10px rgba(252, 112, 1, 0.2);
            transform: translateY(-2px);
        }
        
        /* Pulse animation for CTA */
        @keyframes softPulse {
            0% { box-shadow: 0 0 0 0 rgba(252, 112, 1, 0.4); }
            70% { box-shadow: 0 0 0 10px rgba(252, 112, 1, 0); }
            100% { box-shadow: 0 0 0 0 rgba(252, 112, 1, 0); }
        }
        
        .pulse-btn {
            animation: softPulse 2s infinite;
        }
    </style>
</head>

<body class="bg-white font-sans">

<!-- NAVIGATION - Using your colors -->
<nav class="bg-white shadow-md fixed w-full z-50 border-b border-[#FC7001]/20">
    <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
        <div class="flex items-center space-x-2">
            <div class="w-2 h-8 bg-[#FC7001] rounded-full"></div>
            <div class="w-2 h-8 bg-[#DD723C] rounded-full"></div>
            <div class="w-2 h-8 bg-[#AD4A28] rounded-full"></div>
            <span class="text-2xl font-bold ml-2" style="color: #AD4A28;">SmartBiz</span>
        </div>

        <div class="space-x-6">
            <a href="#features" class="text-gray-600 hover:text-[#FC7001] transition">Features</a>
            <a href="#pricing" class="text-gray-600 hover:text-[#FC7001] transition">Pricing</a>
            <a href="#contact" class="text-gray-600 hover:text-[#FC7001] transition">Contact</a>
            <a href="login.php" class="bg-[#FC7001] text-white px-5 py-2 rounded-lg hover:bg-[#DD723C] transition shadow-lg">
                <i class="fas fa-sign-in-alt mr-1"></i> Login
            </a>
        </div>
    </div>
</nav>

<!-- HERO SECTION - Using your gradient -->
<section class="pt-32 pb-20 px-6 relative overflow-hidden">
    <!-- Background decorative elements using your colors -->
    <div class="absolute top-20 right-10 w-64 h-64 bg-[#FC7001]/5 rounded-full blur-3xl"></div>
    <div class="absolute bottom-10 left-10 w-80 h-80 bg-[#DD723C]/5 rounded-full blur-3xl"></div>
    
    <div class="max-w-6xl mx-auto text-center relative">
        <!-- Badge -->
        <div class="inline-flex items-center bg-[#FC7001]/10 px-4 py-2 rounded-full mb-8">
            <span class="w-2 h-2 bg-[#FC7001] rounded-full mr-2"></span>
            <span style="color: #AD4A28;" class="font-medium">Built for All small businesses</span>
        </div>
        
        <h1 class="text-5xl md:text-6xl font-extrabold mb-6">
            <span style="color: #AD4A28;">Easy Billing</span><br>
            <span style="color: #FC7001;">Inventory Software for Small Shops</span>
        </h1>
        
        <p class="text-xl text-gray-600 max-w-3xl mx-auto mb-10">
            Complete billing, inventory, and customer management for 
            <span style="color: #FC7001;">juice shops, restaurants, dress shops</span> 
            and small businesses.
        </p>

        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="login.php" 
               class="bg-[#FC7001] text-white px-8 py-4 rounded-lg text-lg font-semibold hover:bg-[#DD723C] transition shadow-xl pulse-btn">
                <i class="fas fa-play-circle mr-2"></i>
                Try Live Demo
            </a>
            <a href="https://wa.me/91XXXXXXXXXX?text=Hi%20I%20am%20interested%20in%20SmartBiz%20installation"
               target="_blank"
               class="border-2 border-[#FC7001] text-[#AD4A28] px-8 py-4 rounded-lg text-lg font-semibold hover:bg-[#FC7001] hover:text-white transition">
                <i class="fab fa-whatsapp mr-2"></i>
                Chat on WhatsApp
            </a>
        </div>
    </div>
</section>

<!-- FEATURES SECTION -->
<section id="features" class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-6">
        <!-- Section header -->
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold mb-4" style="color: #AD4A28;">Everything You Need</h2>
            <div class="w-24 h-1 bg-[#FC7001] mx-auto"></div>
            <p class="text-gray-600 mt-4 text-lg">One system that grows with your business</p>
        </div>

        <!-- Features grid -->
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            
            <!-- Feature 1 -->
            <div class="bg-white p-8 rounded-xl shadow-lg feature-card">
                <div class="w-14 h-14 bg-[#FC7001]/10 rounded-xl flex items-center justify-center mb-6">
                    <i class="fas fa-file-invoice text-2xl" style="color: #FC7001;"></i>
                </div>
                <h3 class="text-xl font-bold mb-3" style="color: #AD4A28;">GST Ready Billing</h3>
                <p class="text-gray-600">Create professional invoices with auto GST calculation. Print or share via WhatsApp instantly.</p>
                <div class="mt-4 flex items-center text-sm" style="color: #DD723C;">
                    <span>Juice shops • Restaurants • Retail</span>
                </div>
            </div>

            <!-- Feature 2 -->
            <div class="bg-white p-8 rounded-xl shadow-lg feature-card">
                <div class="w-14 h-14 bg-[#FC7001]/10 rounded-xl flex items-center justify-center mb-6">
                    <i class="fas fa-boxes text-2xl" style="color: #FC7001;"></i>
                </div>
                <h3 class="text-xl font-bold mb-3" style="color: #AD4A28;">Smart Inventory</h3>
                <p class="text-gray-600">Track stock levels in real-time. Get low stock alerts before you run out.</p>
                <div class="mt-4 flex items-center text-sm" style="color: #DD723C;">
                    <span>Perfect for retail & restaurant stock</span>
                </div>
            </div>

            <!-- Feature 3 -->
            <div class="bg-white p-8 rounded-xl shadow-lg feature-card">
                <div class="w-14 h-14 bg-[#FC7001]/10 rounded-xl flex items-center justify-center mb-6">
                    <i class="fas fa-users text-2xl" style="color: #FC7001;"></i>
                </div>
                <h3 class="text-xl font-bold mb-3" style="color: #AD4A28;">Customer CRM</h3>
                <p class="text-gray-600">Build customer database. Track purchase history and send offers.</p>
                <div class="mt-4 flex items-center text-sm" style="color: #DD723C;">
                    <span>Know your regular customers</span>
                </div>
            </div>

            <!-- Feature 4 -->
            <div class="bg-white p-8 rounded-xl shadow-lg feature-card">
                <div class="w-14 h-14 bg-[#FC7001]/10 rounded-xl flex items-center justify-center mb-6">
                    <i class="fas fa-chart-pie text-2xl" style="color: #FC7001;"></i>
                </div>
                <h3 class="text-xl font-bold mb-3" style="color: #AD4A28;">Sales Reports</h3>
                <p class="text-gray-600">Daily, monthly, and yearly reports. Know exactly how much you're earning.</p>
                <div class="mt-4 flex items-center text-sm" style="color: #DD723C;">
                    <span>Make data-driven decisions</span>
                </div>
            </div>

            <!-- Feature 5 -->
            <div class="bg-white p-8 rounded-xl shadow-lg feature-card">
                <div class="w-14 h-14 bg-[#FC7001]/10 rounded-xl flex items-center justify-center mb-6">
                    <i class="fas fa-mobile-alt text-2xl" style="color: #FC7001;"></i>
                </div>
                <h3 class="text-xl font-bold mb-3" style="color: #AD4A28;">Works on Any Device</h3>
                <p class="text-gray-600">Use on desktop, tablet, or mobile. Access from anywhere.</p>
                <div class="mt-4 flex items-center text-sm" style="color: #DD723C;">
                    <span>No app installation needed</span>
                </div>
            </div>

            <!-- Feature 6 -->
            <div class="bg-white p-8 rounded-xl shadow-lg feature-card">
                <div class="w-14 h-14 bg-[#FC7001]/10 rounded-xl flex items-center justify-center mb-6">
                    <i class="fas fa-cog text-2xl" style="color: #FC7001;"></i>
                </div>
                <h3 class="text-xl font-bold mb-3" style="color: #AD4A28;">Personal Setup & Support</h3>
                <p class="text-gray-600">Add your logo, business name, GST settings. Matches your brand.</p>
                <div class="mt-4 flex items-center text-sm" style="color: #DD723C;">
                    <span>Make it yours</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- INDUSTRY SOLUTIONS -->
<section class="py-20">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold mb-4" style="color: #AD4A28;">Built for Your Business</h2>
            <div class="w-24 h-1 bg-[#FC7001] mx-auto"></div>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            <!-- Juice Shop -->
            <div class="relative group">
                <div class="absolute inset-0 bg-gradient-to-r from-[#FC7001] to-[#AD4A28] rounded-2xl opacity-0 group-hover:opacity-10 transition"></div>
                <div class="bg-white p-8 rounded-2xl border border-gray-100 relative">
                    <i class="fas fa-glass-cheers text-4xl mb-4" style="color: #FC7001;"></i>
                    <h3 class="text-2xl font-bold mb-2" style="color: #AD4A28;">Juice Shops</h3>
                    <p class="text-gray-600 mb-4">Fast billing, ingredient tracking, expiry alerts</p>
                    <ul class="space-y-2 text-sm text-gray-500">
                        <li>✓ 30-second billing</li>
                        <li>✓ Track fruit wastage</li>
                        <li>✓ Popular items report</li>
                    </ul>
                </div>
            </div>

            <!-- Restaurant -->
            <div class="relative group">
                <div class="absolute inset-0 bg-gradient-to-r from-[#FC7001] to-[#AD4A28] rounded-2xl opacity-0 group-hover:opacity-10 transition"></div>
                <div class="bg-white p-8 rounded-2xl border border-gray-100 relative">
                    <i class="fas fa-utensils text-4xl mb-4" style="color: #FC7001;"></i>
                    <h3 class="text-2xl font-bold mb-2" style="color: #AD4A28;">Restaurants</h3>
                    <p class="text-gray-600 mb-4">Table management, kitchen orders, takeaway</p>
                    <ul class="space-y-2 text-sm text-gray-500">
                        <li>✓ Multiple tables</li>
                        <li>✓ Split bills</li>
                        <li>✓ Zomato/Swiggy sync</li>
                    </ul>
                </div>
            </div>

            <!-- Dress Shop -->
            <div class="relative group">
                <div class="absolute inset-0 bg-gradient-to-r from-[#FC7001] to-[#AD4A28] rounded-2xl opacity-0 group-hover:opacity-10 transition"></div>
                <div class="bg-white p-8 rounded-2xl border border-gray-100 relative">
                    <i class="fas fa-tshirt text-4xl mb-4" style="color: #FC7001;"></i>
                    <h3 class="text-2xl font-bold mb-2" style="color: #AD4A28;">Dress Shops</h3>
                    <p class="text-gray-600 mb-4">Size-wise inventory, customer preferences</p>
                    <ul class="space-y-2 text-sm text-gray-500">
                        <li>✓ Size & color tracking</li>
                        <li>✓ Loyalty points</li>
                        <li>✓ Seasonal trends</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- PRICING SECTION -->
<section id="pricing" class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold mb-4" style="color: #AD4A28;">Simple, Transparent Pricing</h2>
            <div class="w-24 h-1 bg-[#FC7001] mx-auto"></div>
            <p class="text-gray-600 mt-4 text-lg">Choose what works for your budget</p>
        </div>

        <div class="grid md:grid-cols-3 gap-8 max-w-5xl mx-auto">
            <!-- Basic Plan -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="p-8">
                    <h3 class="text-xl font-bold mb-2" style="color: #AD4A28;">Micro Business</h3>
                    <div class="mb-4">
                        <span class="text-4xl font-bold" style="color: #FC7001;">₹2,499</span>
                        <span class="text-gray-500">/year</span>
                    </div>
                    <p class="text-gray-600 mb-6">Perfect for juice shops, small stalls</p>
                    <ul class="space-y-3 text-gray-600">
                        <li><i class="fas fa-check mr-2" style="color: #FC7001;"></i> GST Billing</li>
                        <li><i class="fas fa-check mr-2" style="color: #FC7001;"></i> Basic Inventory</li>
                        <li><i class="fas fa-check mr-2" style="color: #FC7001;"></i> 1 User</li>
                        <li><i class="fas fa-check mr-2" style="color: #FC7001;"></i> Email Support</li>
                    </ul>
                </div>
                <div class="p-6 bg-gray-50">
                    <a href="https://wa.me/91XXXXXXXXXX" class="block text-center text-[#FC7001] font-semibold hover:text-[#AD4A28]">
                        Get Started →
                    </a>
                </div>
            </div>

            <!-- Popular Plan -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden transform scale-105 relative">
                <div class="absolute top-0 right-0 bg-[#FC7001] text-white px-4 py-1 text-sm rounded-bl-lg">Popular</div>
                <div class="p-8">
                    <h3 class="text-xl font-bold mb-2" style="color: #AD4A28;">Retail Pro</h3>
                    <div class="mb-4">
                        <span class="text-4xl font-bold" style="color: #FC7001;">₹4,999</span>
                        <span class="text-gray-500">/year</span>
                    </div>
                    <p class="text-gray-600 mb-6">Ideal for dress shops, general stores</p>
                    <ul class="space-y-3 text-gray-600">
                        <li><i class="fas fa-check mr-2" style="color: #FC7001;"></i> Everything in Micro</li>
                        <li><i class="fas fa-check mr-2" style="color: #FC7001;"></i> Advanced Inventory</li>
                        <li><i class="fas fa-check mr-2" style="color: #FC7001;"></i> Customer CRM</li>
                        <li><i class="fas fa-check mr-2" style="color: #FC7001;"></i> Sales Reports</li>
                        <li><i class="fas fa-check mr-2" style="color: #FC7001;"></i> 3 Users</li>
                    </ul>
                </div>
                <div class="p-6 bg-gray-50">
                    <a href="https://wa.me/91XXXXXXXXXX" class="block text-center bg-[#FC7001] text-white px-4 py-2 rounded-lg font-semibold hover:bg-[#DD723C]">
                        Choose This Plan
                    </a>
                </div>
            </div>

            <!-- Restaurant Plan -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="p-8">
                    <h3 class="text-xl font-bold mb-2" style="color: #AD4A28;">Restaurant</h3>
                    <div class="mb-4">
                        <span class="text-4xl font-bold" style="color: #FC7001;">₹7,999</span>
                        <span class="text-gray-500">/year</span>
                    </div>
                    <p class="text-gray-600 mb-6">Complete restaurant management</p>
                    <ul class="space-y-3 text-gray-600">
                        <li><i class="fas fa-check mr-2" style="color: #FC7001;"></i> Everything in Retail</li>
                        <li><i class="fas fa-check mr-2" style="color: #FC7001;"></i> Table Management</li>
                        <li><i class="fas fa-check mr-2" style="color: #FC7001;"></i> Kitchen Display</li>
                        <li><i class="fas fa-check mr-2" style="color: #FC7001;"></i> Online Orders</li>
                        <li><i class="fas fa-check mr-2" style="color: #FC7001;"></i> 5 Users + Waiters</li>
                    </ul>
                </div>
                <div class="p-6 bg-gray-50">
                    <a href="https://wa.me/91XXXXXXXXXX" class="block text-center text-[#FC7001] font-semibold hover:text-[#AD4A28]">
                        Get Started →
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Custom note -->
        <p class="text-center text-gray-500 mt-8">
            * All plans include free updates and 6 months support
        </p>
    </div>
</section>

<!-- WHY CHOOSE US -->
<section class="py-20">
    <div class="max-w-7xl mx-auto px-6">
        <div class="grid md:grid-cols-2 gap-12 items-center">
            <div>
                <h2 class="text-4xl font-bold mb-6" style="color: #AD4A28;">Why Small Businesses Choose Us</h2>
                <div class="space-y-4">
                    <div class="flex items-start">
                        <div class="w-8 h-8 bg-[#FC7001]/10 rounded-full flex items-center justify-center mt-1">
                            <i class="fas fa-check text-sm" style="color: #FC7001;"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="font-semibold text-lg" style="color: #AD4A28;">No Training Needed</h3>
                            <p class="text-gray-600">So simple, your staff can start using it in 10 minutes</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="w-8 h-8 bg-[#FC7001]/10 rounded-full flex items-center justify-center mt-1">
                            <i class="fas fa-check text-sm" style="color: #FC7001;"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="font-semibold text-lg" style="color: #AD4A28;">Works Offline</h3>
                            <p class="text-gray-600">Internet down? No problem. Keep billing and sync later</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="w-8 h-8 bg-[#FC7001]/10 rounded-full flex items-center justify-center mt-1">
                            <i class="fas fa-check text-sm" style="color: #FC7001;"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="font-semibold text-lg" style="color: #AD4A28;">Free WhatsApp Support</h3>
                            <p class="text-gray-600">Get help instantly on WhatsApp. No call centers</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 p-8 rounded-2xl">
                <div class="flex items-center space-x-2 mb-6">
                    <div class="w-2 h-8 bg-[#FC7001] rounded-full"></div>
                    <div class="w-2 h-8 bg-[#DD723C] rounded-full"></div>
                    <div class="w-2 h-8 bg-[#AD4A28] rounded-full"></div>
                    <span class="ml-2 font-semibold" style="color: #AD4A28;">Trusted by business owners</span>
                </div>
                <div class="space-y-4">
                    <div class="bg-white p-4 rounded-lg">
                        <p class="text-gray-600">"Started with juice shop, now using for my restaurant too. Best decision."</p>
                        <p class="text-sm mt-2" style="color: #FC7001;">— Kumar, FreshPress Juices</p>
                    </div>
                    <div class="bg-white p-4 rounded-lg">
                        <p class="text-gray-600">"Inventory tracking saved me ₹5,000 in first month itself."</p>
                        <p class="text-sm mt-2" style="color: #FC7001;">— Priya, Fashion Street</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA SECTION -->
<section class="gradient-custom py-20">
    <div class="max-w-4xl mx-auto text-center px-6">
        <h2 class="text-4xl font-bold text-white mb-4">Start Your Free Demo Today</h2>
        <p class="text-white/90 text-lg mb-8">No credit card required. 30-day money-back guarantee.</p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="login.php" 
               class="bg-white text-[#AD4A28] px-8 py-4 rounded-lg text-lg font-semibold hover:bg-gray-100 transition shadow-xl">
                <i class="fas fa-play-circle mr-2"></i>
                Try Demo Now
            </a>
            <a href="https://wa.me/91XXXXXXXXXX" 
               class="border-2 border-white text-white px-8 py-4 rounded-lg text-lg font-semibold hover:bg-white hover:text-[#AD4A28] transition">
                <i class="fab fa-whatsapp mr-2"></i>
                Talk to Sales
            </a>
        </div>
    </div>
</section>

<!-- CONTACT SECTION -->
<section id="contact" class="py-16 bg-white">
    <div class="max-w-4xl mx-auto text-center px-6">
        <h3 class="text-2xl font-bold mb-4" style="color: #AD4A28;">Get in Touch</h3>
        <div class="w-16 h-0.5 bg-[#FC7001] mx-auto mb-6"></div>
        
        <div class="grid md:grid-cols-3 gap-6 mt-8">
            <div>
                <i class="fas fa-map-marker-alt text-2xl mb-2" style="color: #FC7001;"></i>
                <p class="text-gray-600">Chennai, India</p>
            </div>
            <div>
                <i class="fas fa-envelope text-2xl mb-2" style="color: #FC7001;"></i>
                <p class="text-gray-600">sales@smartbiz.com</p>
            </div>
            <div>
                <i class="fab fa-whatsapp text-2xl mb-2" style="color: #FC7001;"></i>
                <p class="text-gray-600">+91 8778373517</p>
            </div>
        </div>
    </div>
</section>

<!-- FOOTER -->
<footer class="bg-gray-900 text-white py-12">
    <div class="max-w-7xl mx-auto px-6">
        <div class="grid md:grid-cols-4 gap-8">
            <div>
                <div class="flex items-center space-x-2 mb-4">
                    <div class="w-2 h-8 bg-[#FC7001] rounded-full"></div>
                    <div class="w-2 h-8 bg-[#DD723C] rounded-full"></div>
                    <div class="w-2 h-8 bg-[#AD4A28] rounded-full"></div>
                    <span class="text-xl font-bold ml-2">SmartBiz</span>
                </div>
                <p class="text-gray-400 text-sm">Simple software for smart businesses</p>
            </div>
            
            <div>
                <h4 class="font-semibold mb-4" style="color: #FC7001;">Product</h4>
                <ul class="space-y-2 text-gray-400">
                    <li><a href="#features" class="hover:text-white">Features</a></li>
                    <li><a href="#pricing" class="hover:text-white">Pricing</a></li>
                    <li><a href="#" class="hover:text-white">Demo</a></li>
                </ul>
	    </div>

            
            <div>
                <h4 class="font-semibold mb-4" style="color: #FC7001;">Support</h4>
                <ul class="space-y-2 text-gray-400">
                    <li><a href="#" class="hover:text-white">Help Center</a></li>
                    <li><a href="#" class="hover:text-white">WhatsApp Support</a></li>
                    <li><a href="#contact" class="hover:text-white">Contact</a></li>
                </ul>
            </div>
            
            <div>
                <h4 class="font-semibold mb-4" style="color: #FC7001;">Connect</h4>
                <div class="flex space-x-4">
                    <a href="https://github.com/GirtharanJ" target="_blank" class="text-gray-400 hover:text-white text-xl">
                        <i class="fab fa-github"></i>
                    </a>
                    <a href="https://www.linkedin.com/in/giritharanj" target="_blank" class="text-gray-400 hover:text-white text-xl">
                        <i class="fab fa-linkedin"></i>
                    </a>
                    <a href="https://wa.me/+918778373517" target="_blank" class="text-gray-400 hover:text-white text-xl">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                </div>
            </div>
        </div>
        
        <div class="border-t border-gray-800 mt-12 pt-8 text-center text-gray-400 text-sm">
            <p>© <?php echo date('Y'); ?> SmartBiz. All rights reserved.</p>
            <p class="mt-2">Designed for juice shops, restaurants, and small businesses</p>
        </div>
    </div>
</footer>

</body>
</html>
