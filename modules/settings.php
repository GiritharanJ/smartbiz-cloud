<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../components/header.php';


if (!isAdmin()) {
    redirect('../dashboard.php');
}

$db = new Database();
$pdo = $db->connect();

// Handle settings update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST as $key => $value) {
        if ($key !== 'action') {
            $stmt = $pdo->prepare("INSERT INTO settings (key, value) VALUES (?, ?) ON CONFLICT (key) DO UPDATE SET value = ?");
            $stmt->execute([$key, $value, $value]);
        }
    }
    
    // Handle logo upload
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['logo']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $newFilename = 'logo_' . time() . '.' . $ext;
            $uploadPath = '../assets/uploads/' . $newFilename;
            
            if (move_uploaded_file($_FILES['logo']['tmp_name'], $uploadPath)) {
                $stmt = $pdo->prepare("INSERT INTO settings (key, value) VALUES ('business_logo', ?) ON CONFLICT (key) DO UPDATE SET value = ?");
                $stmt->execute([$newFilename, $newFilename]);
            }
        }
    }
    
    // Handle user management
    if (isset($_POST['action']) && $_POST['action'] === 'add_user') {
        $hashedPassword = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$_POST['name'], $_POST['email'], $hashedPassword, $_POST['role']]);
    }
    
    $success = "Settings updated successfully!";
}

// Get current settings
$settings = [];
$stmt = $pdo->query("SELECT * FROM settings");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $settings[$row['key']] = $row['value'];
}

// Get users for management
$users = $pdo->query("SELECT id, name, email, role FROM users ORDER BY created_at DESC")->fetchAll();
?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Business Settings -->
    <div class="lg:col-span-2 bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-lg font-semibold text-[#4d2c0b] mb-4">Business Settings</h2>
        
        <?php if (isset($success)): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="mb-4">
                    <label class="block text-[#4d2c0b] text-sm font-bold mb-2">Business Name</label>
                    <input type="text" name="business_name" value="<?php echo $settings['business_name'] ?? 'SmartBiz'; ?>"
                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-[#713600]">
                </div>
                
                <div class="mb-4">
                    <label class="block text-[#4d2c0b] text-sm font-bold mb-2">Currency</label>
                    <select name="currency" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-[#713600]">
                        <option value="₹" <?php echo ($settings['currency'] ?? '₹') === '₹' ? 'selected' : ''; ?>>Indian Rupee (₹)</option>
                        <option value="$" <?php echo ($settings['currency'] ?? '') === '$' ? 'selected' : ''; ?>>US Dollar ($)</option>
                        <option value="€" <?php echo ($settings['currency'] ?? '') === '€' ? 'selected' : ''; ?>>Euro (€)</option>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label class="block text-[#4d2c0b] text-sm font-bold mb-2">GST Rate (%)</label>
                    <input type="number" name="gst_rate" value="<?php echo $settings['gst_rate'] ?? '18'; ?>" step="0.01"
                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-[#713600]">
                </div>
                
                <div class="mb-4">
                    <label class="block text-[#4d2c0b] text-sm font-bold mb-2">GST Enabled</label>
                    <select name="gst_enabled" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-[#713600]">
                        <option value="true" <?php echo ($settings['gst_enabled'] ?? 'true') === 'true' ? 'selected' : ''; ?>>Yes</option>
                        <option value="false" <?php echo ($settings['gst_enabled'] ?? '') === 'false' ? 'selected' : ''; ?>>No</option>
                    </select>
                </div>
                
                <div class="mb-4 md:col-span-2">
                    <label class="block text-[#4d2c0b] text-sm font-bold mb-2">Business Logo</label>
                    <input type="file" name="logo" accept="image/*"
                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-[#713600]">
                    <?php if (!empty($settings['business_logo'])): ?>
                        <div class="mt-2">
                            <img src="../assets/uploads/<?php echo $settings['business_logo']; ?>" alt="Logo" class="h-16">
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <button type="submit" class="bg-[#713600] text-white px-6 py-2 rounded-lg hover:bg-[#4d2c0b]">
                Save Settings
            </button>
        </form>
    </div>
    
    <!-- User Management -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-lg font-semibold text-[#4d2c0b] mb-4">User Management</h2>
        
        <form method="POST" class="mb-4">
            <input type="hidden" name="action" value="add_user">
            
            <div class="mb-3">
                <label class="block text-[#4d2c0b] text-sm font-bold mb-2">Name</label>
                <input type="text" name="name" required
                       class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-[#713600]">
            </div>
            
            <div class="mb-3">
                <label class="block text-[#4d2c0b] text-sm font-bold mb-2">Email</label>
                <input type="email" name="email" required
                       class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-[#713600]">
            </div>
            
            <div class="mb-3">
                <label class="block text-[#4d2c0b] text-sm font-bold mb-2">Password</label>
                <input type="password" name="password" required
                       class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-[#713600]">
            </div>
            
            <div class="mb-3">
                <label class="block text-[#4d2c0b] text-sm font-bold mb-2">Role</label>
                <select name="role" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-[#713600]">
                    <option value="staff">Staff</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            
            <button type="submit" class="w-full bg-[#713600] text-white px-4 py-2 rounded hover:bg-[#4d2c0b]">
                Add User
            </button>
        </form>
        
        <h3 class="font-semibold text-[#4d2c0b] mb-2">Current Users</h3>
        <div class="space-y-2">
            <?php foreach ($users as $user): ?>
            <div class="flex justify-between items-center p-2 border rounded">
                <div>
                    <p class="font-medium"><?php echo $user['name']; ?></p>
                    <p class="text-sm text-gray-600"><?php echo $user['email']; ?></p>
                </div>
                <span class="px-2 py-1 text-xs rounded <?php echo $user['role'] === 'admin' ? 'bg-[#713600] text-white' : 'bg-gray-200'; ?>">
                    <?php echo $user['role']; ?>
                </span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../components/footer.php'; ?>
