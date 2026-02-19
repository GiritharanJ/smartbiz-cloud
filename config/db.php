<?php
// config/db.php
class Database {
    private $host = 'localhost';
    private $port = '5432';
    private $dbname = 'smartbiz';
    private $username = 'postgres';
    private $password = 'postgres'; // Change to your password
    private $pdo;

    public function connect() {
        try {
            $dsn = "pgsql:host=$this->host;port=$this->port;dbname=$this->dbname;";
            
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
            die("Connection failed: " . $e->getMessage());
        }
    }
}

// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Helper functions
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
?>
