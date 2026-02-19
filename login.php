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
    <title>SmartBiz - Secure Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Font Awesome 6 (Free) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <!-- Google Fonts - Poppins for modern look -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        /* Custom styles using #FF7F50 */
        :root {
            --primary: #FF7F50;
            --primary-light: #FFA07A;
            --primary-dark: #E5673A;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
        }
        
        .bg-primary { background-color: var(--primary); }
        .text-primary { color: var(--primary); }
        .border-primary { border-color: var(--primary); }
        
        .hover\:bg-primary:hover { background-color: var(--primary-dark); }
        .hover\:text-primary:hover { color: var(--primary); }
        
        .focus\:ring-primary:focus { --tw-ring-color: var(--primary); }
        
        /* Gradient background */
        .bg-gradient-custom {
            background: linear-gradient(135deg, #FF7F50 0%, #FF6347 50%, #FF4500 100%);
        }
        
        /* Floating animation */
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
        
        .float-animation {
            animation: float 4s ease-in-out infinite;
        }
        
        /* Pulse effect for demo box */
        @keyframes softPulse {
            0% { box-shadow: 0 0 0 0 rgba(255, 127, 80, 0.4); }
            70% { box-shadow: 0 0 0 15px rgba(255, 127, 80, 0); }
            100% { box-shadow: 0 0 0 0 rgba(255, 127, 80, 0); }
        }
        
        .demo-pulse {
            animation: softPulse 2s infinite;
        }
        
        /* Input focus effect */
        .input-focus-effect:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(255, 127, 80, 0.2);
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-custom flex items-center justify-center p-4 relative overflow-hidden">

    <!-- Decorative background elements -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0">
        <div class="absolute top-20 left-20 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-20 right-20 w-96 h-96 bg-white/10 rounded-full blur-3xl"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full h-full bg-white/5 rounded-full blur-3xl"></div>
    </div>
    
    <!-- Main Login Card -->
    <div class="bg-white/95 backdrop-blur-sm shadow-2xl rounded-3xl w-full max-w-md p-8 relative z-10 transform transition-all duration-500 hover:scale-105">
        
        <!-- Logo Section with Floating Icon -->
        <div class="text-center mb-8">
            <div class="flex justify-center mb-4">
                <div class="w-20 h-20 bg-gradient-to-br from-[#FF7F50] to-[#FF4500] rounded-2xl flex items-center justify-center shadow-lg float-animation">
                    <i class="fas fa-store-alt text-white text-4xl"></i>
                </div>
            </div>
            <h1 class="text-4xl font-bold bg-gradient-to-r from-[#FF7F50] to-[#FF4500] bg-clip-text text-transparent">
                SmartBiz
            </h1>
            <p class="text-gray-500 mt-2 font-medium">Petrol Bunk Management System</p>
        </div>
        
        <!-- Error Message with Icon -->
        <?php if (!empty($error)) : ?>
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-6 flex items-center">
                <i class="fas fa-exclamation-circle text-red-500 mr-3 text-xl"></i>
                <span class="text-sm font-medium"><?php echo htmlspecialchars($error); ?></span>
            </div>
        <?php endif; ?>
        
        <!-- Login Form -->
        <form method="POST" class="space-y-6">
            <!-- Email Field -->
            <div>
                <label class="block text-gray-700 font-medium mb-2">
                    <i class="fas fa-envelope text-[#FF7F50] mr-2"></i>Email Address
                </label>
                <div class="relative">
                    <input type="email" name="email" required value="admin@smartbiz.com"
                        class="w-full px-4 py-3 pl-12 border border-gray-300 rounded-xl focus:outline-none input-focus-effect transition-all duration-300"
                        placeholder="Enter your email">
                    <i class="fas fa-envelope absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>
            
            <!-- Password Field -->
            <div>
                <label class="block text-gray-700 font-medium mb-2">
                    <i class="fas fa-lock text-[#FF7F50] mr-2"></i>Password
                </label>
                <div class="relative">
                    <input type="password" name="password" required value="admin123"
                        class="w-full px-4 py-3 pl-12 border border-gray-300 rounded-xl focus:outline-none input-focus-effect transition-all duration-300"
                        placeholder="Enter your password">
                    <i class="fas fa-lock absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <button type="button" onclick="togglePassword()" class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-[#FF7F50]">
                        <i class="fas fa-eye" id="toggleIcon"></i>
                    </button>
                </div>
            </div>
            
            <!-- Remember Me & Forgot Password -->
            <div class="flex items-center justify-between">
                <label class="flex items-center">
                    <input type="checkbox" class="rounded border-gray-300 text-[#FF7F50] focus:ring-[#FF7F50]">
                    <span class="ml-2 text-sm text-gray-600">Remember me</span>
                </label>
                <a href="#" class="text-sm text-[#FF7F50] hover:text-[#FF4500] font-medium">
                    Forgot Password?
                </a>
            </div>
            
            <!-- Login Button -->
            <button type="submit"
                class="w-full bg-gradient-to-r from-[#FF7F50] to-[#FF4500] hover:from-[#FF4500] hover:to-[#FF7F50] text-white py-3 rounded-xl font-semibold text-lg transition-all duration-300 transform hover:scale-[1.02] hover:shadow-xl flex items-center justify-center group">
                <span>Login</span>
                <i class="fas fa-arrow-right ml-2 group-hover:translate-x-2 transition-transform duration-300"></i>
            </button>
        </form>
        
        <!-- Demo Credentials Box - Attractive and Eye-catching -->
        <div class="mt-8 bg-gradient-to-r from-orange-50 to-amber-50 p-6 rounded-2xl border-2 border-[#FF7F50] shadow-xl demo-pulse relative overflow-hidden">
            <!-- Decorative corner -->
            <div class="absolute top-0 right-0 w-16 h-16 bg-[#FF7F50]/10 rounded-bl-3xl"></div>
            
            <div class="flex items-center mb-3">
                <div class="bg-[#FF7F50] text-white px-3 py-1 rounded-full text-xs font-bold mr-2">
                    DEMO
                </div>
                <span class="text-sm text-gray-500">Quick Access Credentials</span>
            </div>
            
            <div class="space-y-3">
                <div class="flex items-center bg-white p-3 rounded-xl">
                    <div class="w-8 h-8 bg-[#FF7F50]/10 rounded-full flex items-center justify-center mr-3">
                        <i class="fas fa-envelope text-[#FF7F50]"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Email</p>
                        <p class="font-bold text-gray-800">admin@smartbiz.com</p>
                    </div>
                    <button onclick="copyToClipboard('admin@smartbiz.com')" class="ml-auto text-gray-400 hover:text-[#FF7F50] transition-colors">
                        <i class="far fa-copy"></i>
                    </button>
                </div>
                
                <div class="flex items-center bg-white p-3 rounded-xl">
                    <div class="w-8 h-8 bg-[#FF7F50]/10 rounded-full flex items-center justify-center mr-3">
                        <i class="fas fa-lock text-[#FF7F50]"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Password</p>
                        <p class="font-bold text-gray-800">admin123</p>
                    </div>
                    <button onclick="copyToClipboard('admin123')" class="ml-auto text-gray-400 hover:text-[#FF7F50] transition-colors">
                        <i class="far fa-copy"></i>
                    </button>
                </div>
            </div>
            
            <!-- Quick Login Button -->
            <button onclick="quickLogin()" class="mt-4 w-full bg-[#FF7F50] text-white py-2 rounded-xl font-medium hover:bg-[#FF4500] transition-colors flex items-center justify-center">
                <i class="fas fa-rocket mr-2"></i>
                Quick Login with Demo Account
            </button>
        </div>
        
        <!-- Footer Links -->
        <div class="mt-6 text-center text-sm text-gray-500">
            <span>New to SmartBiz?</span>
            <a href="#" class="text-[#FF7F50] hover:text-[#FF4500] font-medium ml-1">Contact Sales</a>
        </div>
    </div>
    
    <!-- Toast Notification for Copy -->
    <div id="toast" class="fixed bottom-4 right-4 bg-gray-800 text-white px-4 py-2 rounded-lg shadow-lg transform transition-all duration-500 translate-y-20 opacity-0">
        <i class="fas fa-check-circle text-green-400 mr-2"></i>
        <span id="toastMessage">Copied to clipboard!</span>
    </div>
    
    <script>
        // Toggle password visibility
        function togglePassword() {
            const passwordInput = document.querySelector('input[name="password"]');
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
        
        // Copy to clipboard function
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                showToast('Copied: ' + text);
            });
        }
        
        // Quick login function
        function quickLogin() {
            // Fill the form
            document.querySelector('input[name="email"]').value = 'admin@smartbiz.com';
            document.querySelector('input[name="password"]').value = 'admin123';
            
            // Add highlight effect
            document.querySelector('input[name="email"]').classList.add('ring-2', 'ring-[#FF7F50]');
            document.querySelector('input[name="password"]').classList.add('ring-2', 'ring-[#FF7F50]');
            
            // Show toast
            showToast('Credentials filled! Click Login');
            
            // Remove highlight after 2 seconds
            setTimeout(() => {
                document.querySelector('input[name="email"]').classList.remove('ring-2', 'ring-[#FF7F50]');
                document.querySelector('input[name="password"]').classList.remove('ring-2', 'ring-[#FF7F50]');
            }, 2000);
        }
        
        // Toast notification
        function showToast(message) {
            const toast = document.getElementById('toast');
            const toastMessage = document.getElementById('toastMessage');
            
            toastMessage.textContent = message;
            toast.classList.remove('translate-y-20', 'opacity-0');
            
            setTimeout(() => {
                toast.classList.add('translate-y-20', 'opacity-0');
            }, 2000);
        }
        
        // Auto-fill demo credentials on page load (optional)
        window.onload = function() {
            // Uncomment below if you want auto-fill
            // quickLogin();
        };
    </script>
    
    <!-- Additional decorative elements -->
    <div class="absolute bottom-5 left-5 text-white/20 text-xs z-0">
        <i class="fas fa-circle"></i>
        <i class="fas fa-circle ml-2"></i>
        <i class="fas fa-circle ml-2"></i>
    </div>
</body>
</html>
