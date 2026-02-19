<?php
session_start();

// If already logged in, redirect to dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'config/db-loader.php';
    require_once 'config/init.php';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    try {
        $db = new Database();
        $pdo = $db->connect();
        
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['role'] = $user['role'];
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Invalid email or password";
        }
    } catch (PDOException $e) {
        $error = "Database connection error";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SmartBiz - Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <!-- Google Fonts - Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        :root {
            --primary: #FF7F50;
            --primary-light: #FF6347;
            --primary-dark: #FF4500;
        }
        
        body {
            font-family: 'Inter', sans-serif;
        }
        
        .bg-gradient-custom {
            background: linear-gradient(135deg, #FF7F50 0%, #FF6347 50%, #FF4500 100%);
        }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-5px); }
            100% { transform: translateY(0px); }
        }
        
        .float-animation {
            animation: float 4s ease-in-out infinite;
        }

        @keyframes softPulse {
            0% { box-shadow: 0 0 0 0 rgba(255, 127, 80, 0.4); }
            70% { box-shadow: 0 0 0 10px rgba(255, 127, 80, 0); }
            100% { box-shadow: 0 0 0 0 rgba(255, 127, 80, 0); }
        }
        
        .demo-pulse {
            animation: softPulse 2s infinite;
        }

        .input-focus-effect:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(255, 127, 80, 0.15);
        }
        
        .logo-container {
            width: 48px;
            height: 48px;
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-custom flex items-center justify-center p-3 relative overflow-hidden">

    <!-- Decorative background -->
    <div class="absolute inset-0 overflow-hidden z-0">
        <div class="absolute top-10 left-10 w-48 h-48 bg-white/5 rounded-full blur-3xl"></div>
        <div class="absolute bottom-10 right-10 w-64 h-64 bg-white/5 rounded-full blur-3xl"></div>
    </div>
    
    <!-- Main Login Card -->
    <div class="bg-white/95 backdrop-blur-sm shadow-xl rounded-2xl w-full max-w-[360px] p-5 relative z-10">
        
        <!-- Logo Section -->
        <div class="text-center mb-4">
            <div class="flex justify-center mb-2">
                <div class="logo-container bg-gradient-to-br from-[#FF7F50] to-[#FF4500] rounded-xl flex items-center justify-center shadow-md float-animation">
                    <i class="fas fa-store-alt text-white text-xl"></i>
                </div>
            </div>
            <h1 class="text-2xl font-bold bg-gradient-to-r from-[#FF7F50] to-[#FF4500] bg-clip-text text-transparent">
                SmartBiz
            </h1>
            <p class="text-gray-500 text-xs mt-0.5">Business Management System</p>
        </div>
        
        <!-- Error Message -->
        <?php if (!empty($error)) : ?>
            <div class="bg-red-50 border-l-3 border-red-500 text-red-700 p-2 rounded-lg mb-3 text-xs flex items-center">
                <i class="fas fa-exclamation-circle text-red-500 mr-2 text-sm"></i>
                <span><?php echo htmlspecialchars($error); ?></span>
            </div>
        <?php endif; ?>
        
        <!-- Login Form -->
        <form method="POST" class="space-y-3.5" id="loginForm">
            <!-- Email Field - WITHOUT default value -->
            <div>
                <label class="block text-gray-600 text-xs font-medium mb-1">
                    <i class="fas fa-envelope text-[#FF7F50] mr-1 text-xs"></i>Email
                </label>
                <div class="relative">
                    <input type="email" name="email" id="emailInput" required
                        class="w-full px-3 py-2 pl-8 text-sm border border-gray-200 rounded-lg focus:outline-none input-focus-effect transition-all"
                        placeholder="Enter email" value="">
                    <i class="fas fa-envelope absolute left-2.5 top-1/2 transform -translate-y-1/2 text-gray-300 text-xs"></i>
                </div>
            </div>
            
            <!-- Password Field - WITHOUT default value -->
            <div>
                <label class="block text-gray-600 text-xs font-medium mb-1">
                    <i class="fas fa-lock text-[#FF7F50] mr-1 text-xs"></i>Password
                </label>
                <div class="relative">
                    <input type="password" name="password" id="passwordInput" required
                        class="w-full px-3 py-2 pl-8 text-sm border border-gray-200 rounded-lg focus:outline-none input-focus-effect transition-all"
                        placeholder="Enter password" value="">
                    <i class="fas fa-lock absolute left-2.5 top-1/2 transform -translate-y-1/2 text-gray-300 text-xs"></i>
                    <button type="button" onclick="togglePassword()" class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-[#FF7F50]">
                        <i class="fas fa-eye text-xs" id="toggleIcon"></i>
                    </button>
                </div>
            </div>
            
            <!-- Remember Me & Forgot Password -->
            <div class="flex items-center justify-between text-xs">
                <label class="flex items-center">
                    <input type="checkbox" class="rounded border-gray-300 text-[#FF7F50] focus:ring-[#FF7F50] scale-90">
                    <span class="ml-1 text-gray-500">Remember</span>
                </label>
                <a href="#" class="text-[#FF7F50] hover:text-[#FF4500] text-xs">
                    Forgot?
                </a>
            </div>
            
            <!-- Login Button -->
            <button type="submit"
                class="w-full bg-gradient-to-r from-[#FF7F50] to-[#FF4500] hover:from-[#FF4500] hover:to-[#FF7F50] text-white py-2 rounded-lg font-medium text-sm transition-all hover:shadow-md flex items-center justify-center group">
                <span>Login</span>
                <i class="fas fa-arrow-right ml-1.5 text-xs group-hover:translate-x-1 transition-transform"></i>
            </button>
        </form>
        
        <!-- Demo Credentials Box -->
        <div class="mt-4 bg-gradient-to-r from-orange-50 to-amber-50 p-3 rounded-xl border border-[#FF7F50]/30 demo-pulse">
            <div class="flex items-center mb-2">
                <span class="bg-[#FF7F50] text-white px-2 py-0.5 rounded-full text-[10px] font-bold mr-2">DEMO</span>
                <span class="text-[10px] text-gray-500">Quick Access</span>
            </div>
            
            <div class="space-y-2">
                <!-- Email Row -->
                <div class="flex items-center bg-white p-2 rounded-lg">
                    <div class="w-6 h-6 bg-[#FF7F50]/10 rounded-full flex items-center justify-center mr-2">
                        <i class="fas fa-envelope text-[#FF7F50] text-[10px]"></i>
                    </div>
                    <div>
                        <p class="text-[8px] text-gray-400">Email</p>
                        <p class="font-medium text-gray-800 text-xs">admin@smartbiz.com</p>
                    </div>
                    <button onclick="copyToClipboard('admin@smartbiz.com')" class="ml-auto text-gray-300 hover:text-[#FF7F50] text-xs">
                        <i class="far fa-copy"></i>
                    </button>
                </div>
                
                <!-- Password Row -->
                <div class="flex items-center bg-white p-2 rounded-lg">
                    <div class="w-6 h-6 bg-[#FF7F50]/10 rounded-full flex items-center justify-center mr-2">
                        <i class="fas fa-lock text-[#FF7F50] text-[10px]"></i>
                    </div>
                    <div>
                        <p class="text-[8px] text-gray-400">Password</p>
                        <p class="font-medium text-gray-800 text-xs">admin123</p>
                    </div>
                    <button onclick="copyToClipboard('admin123')" class="ml-auto text-gray-300 hover:text-[#FF7F50] text-xs">
                        <i class="far fa-copy"></i>
                    </button>
                </div>
            </div>
            
            <!-- FIXED: Quick Login Button - Now ALWAYS fills credentials -->
            <button type="button" onclick="fillCredentials()" class="mt-2 w-full bg-[#FF7F50] text-white py-1.5 rounded-lg text-xs font-medium hover:bg-[#FF4500] transition-colors flex items-center justify-center">
                <i class="fas fa-rocket mr-1 text-[10px]"></i>
                Quick Login
            </button>
        </div>
        
        <!-- Footer Link -->
        <div class="mt-3 text-center">
            <a href="#" class="text-[10px] text-gray-400 hover:text-[#FF7F50]">
                <i class="far fa-question-circle mr-1"></i>Need help?
            </a>
        </div>
    </div>
    
    <!-- Toast Notification -->
    <div id="toast" class="fixed bottom-3 right-3 bg-gray-800 text-white px-3 py-1.5 rounded-lg shadow-lg transform transition-all duration-500 translate-y-10 opacity-0 text-xs z-20">
        <i class="fas fa-check-circle text-green-400 mr-1"></i>
        <span id="toastMessage">Copied!</span>
    </div>
    
    <script>
        // Toggle password visibility
        function togglePassword() {
            const passwordInput = document.getElementById('passwordInput');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
        
        // Copy to clipboard
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                showToast('Copied!');
            });
        }
        
        // FIXED: This function now ALWAYS fills the credentials
        function fillCredentials() {
            // Get input elements
            const emailInput = document.getElementById('emailInput');
            const passwordInput = document.getElementById('passwordInput');
            
            // Set values (overwrites whatever is there)
            emailInput.value = 'admin@smartbiz.com';
            passwordInput.value = 'admin123';
            
            // Add visual feedback
            emailInput.classList.add('ring-2', 'ring-[#FF7F50]', 'bg-orange-50');
            passwordInput.classList.add('ring-2', 'ring-[#FF7F50]', 'bg-orange-50');
            
            // Show success message
            showToast('✓ Demo credentials filled!');
            
            // Remove highlight after 1 second
            setTimeout(() => {
                emailInput.classList.remove('ring-2', 'ring-[#FF7F50]', 'bg-orange-50');
                passwordInput.classList.remove('ring-2', 'ring-[#FF7F50]', 'bg-orange-50');
            }, 1000);
            
            // Optional: Auto-submit after filling (uncomment if you want)
            // setTimeout(() => {
            //     document.getElementById('loginForm').submit();
            // }, 500);
        }
        
        // Toast notification
        function showToast(message) {
            const toast = document.getElementById('toast');
            const toastMessage = document.getElementById('toastMessage');
            
            toastMessage.textContent = message;
            toast.classList.remove('translate-y-10', 'opacity-0');
            
            setTimeout(() => {
                toast.classList.add('translate-y-10', 'opacity-0');
            }, 1500);
        }
        
        // Auto-focus email field on page load
        window.onload = function() {
            document.getElementById('emailInput').focus();
        };
    </script>
</body>
</html>
