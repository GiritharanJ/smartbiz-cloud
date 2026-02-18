<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'config/db-loader.php';
require_once 'config/helpers.php';

if (isLoggedIn()) {
    redirect('dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new Database();
    $pdo = $db->connect();
    
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['role'] = $user['role'];
        redirect('dashboard.php');
    } else {
        $error = 'Invalid email or password';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartBiz - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .bg-primary { background-color: #713600; }
        .text-primary { color: #713600; }
        .border-primary { border-color: #713600; }
        .hover\:bg-primary:hover { background-color: #713600; }
    </style>
</head>
<body class="bg-[#fffb8f] min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-xl w-96">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-[#713600]">SmartBiz</h1>
            <p class="text-[#4d2c0b] mt-2">Small Business Management System</p>
        </div>
        
        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="mb-4">
                <label class="block text-[#4d2c0b] text-sm font-bold mb-2">Email</label>
                <input type="email" name="email" required 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#713600]">
            </div>
            
            <div class="mb-6">
                <label class="block text-[#4d2c0b] text-sm font-bold mb-2">Password</label>
                <input type="password" name="password" required 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#713600]">
            </div>
            
            <button type="submit" 
                class="w-full bg-[#713600] text-white font-bold py-2 px-4 rounded-lg hover:bg-[#4d2c0b] transition duration-300">
                Login
            </button>
        </form>
        
        <div class="text-center mt-4 text-sm text-[#4d2c0b]">
            Demo: admin@smartbiz.com / admin123
        </div>
    </div>
</body>
</html>

