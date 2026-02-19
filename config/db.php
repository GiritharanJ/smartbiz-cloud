<?php
// config/db.php - With environment detection

// Prevent multiple declarations
if (!class_exists('Database')) {
    class Database {
        private $pdo;
        
        public function connect() {
            try {
                // Detect if running on Railway
                if (getenv('RAILWAY_SERVICE_NAME')) {
                    // Railway configuration
                    $host = getenv('PGHOST');
                    $port = intval(getenv('PGPORT'));
                    $dbname = getenv('PGDATABASE');
                    $user = getenv('PGUSER');
                    $password = getenv('PGPASSWORD');
                } else {
                    // Local configuration
                    $host = 'localhost';
                    $port = 5432;
                    $dbname = 'smartbiz';
                    $user = 'postgres';
                    $password = 'postgres'; // Change to your password
                }
                
                // Validate configuration
                if (empty($host) || empty($dbname) || empty($user)) {
                    throw new Exception("Database configuration incomplete");
                }
                
                $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;";
                
                $this->pdo = new PDO($dsn, $user, $password, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_TIMEOUT => 5
                ]);
                
                return $this->pdo;
                
            } catch (Exception $e) {
                error_log("Database Error: " . $e->getMessage());
                
                // Show different messages based on environment
                if (getenv('RAILWAY_SERVICE_NAME')) {
                    die("Railway database connection failed. Check your PostgreSQL variables.");
                } else {
                    die("Local database connection failed. Make sure PostgreSQL is running.");
                }
            }
        }
    }
}

// Helper functions (with function_exists checks)
if (!function_exists('isLoggedIn')) {
    function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
}

if (!function_exists('isAdmin')) {
    function isAdmin() {
        return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }
}

if (!function_exists('redirect')) {
    function redirect($url) {
        header("Location: $url");
        exit();
    }
}

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
