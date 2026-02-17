<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// config/db.php
class Database {
    private $host = getenv("DB_HOST");
    private $db   = getenv("DB_NAME");
    private $user = getenv("DB_USER");
    private $pass = getenv("DB_PASSWORD");
    private $port = getenv("DB_PORT");
    private $pdo;

    public function connect() {
        try {
            // Test different connection strings
            $dsn = "pgsql:host=$host;port=$port;dbname=$db;";
            
            // Option 1: With password
            $this->pdo = new PDO(
                $dsn,
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
            
            return $this->pdo;
            
        } catch(PDOException $e) {
            // Detailed error for debugging
            die("Connection failed: " . $e->getMessage() . "<br>
                 Please check:<br>
                 1. PostgreSQL is running: sudo systemctl status postgresql<br>
                 2. Password is correct in config/db.php<br>
                 3. Database 'smartbiz' exists<br>
                 4. User 'postgres' has proper permissions");
        }
    }
}

// Alternative connection method if above fails
class DatabaseAlt {
    private $conn;

    public function connect() {
        try {
            // Try connecting without password first (peer authentication)
            $this->conn = new PDO("pgsql:host=localhost;dbname=smartbiz", 'postgres');
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $this->conn;
        } catch(PDOException $e) {
            // If that fails, try with socket connection
            try {
                $this->conn = new PDO("pgsql:dbname=smartbiz", 'postgres');
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                return $this->conn;
            } catch(PDOException $e2) {
                die("All connection attempts failed. Please configure PostgreSQL properly.");
            }
        }
    }
}

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Helper functions
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function redirect($url) {
    header("Location: $url");
    exit();
}

// Test database connection (optional - can be removed in production)
function testDBConnection() {
    try {
        $db = new Database();
        $conn = $db->connect();
        echo "✅ Database connection successful!";
        return true;
    } catch (Exception $e) {
        echo "❌ Database connection failed: " . $e->getMessage();
        return false;
    }
}
$database = new Database();
$pdo = $database->connect();

?>
