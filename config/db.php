<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

class Database {
    private $host;
    private $port;
    private $dbname;
    private $username;
    private $password;
    private $pdo;

    public function __construct() {
        // Railway Environment Variables
        $this->host     = getenv('DB_HOST') ?: 'localhost';
        $this->port     = getenv('DB_PORT') ?: '5432';
        $this->dbname   = getenv('DB_NAME') ?: 'smartbiz';
        $this->username = getenv('DB_USER') ?: 'postgres';
        $this->password = getenv('DB_PASS') ?: '';
    }

    public function connect() {
        try {
            $dsn = "pgsql:host={$this->host};port={$this->port};dbname={$this->dbname};";

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

        } catch (PDOException $e) {
            die("Database Connection Failed: " . $e->getMessage());
        }
    }
}

// Start session safely
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

// Create global connection
$database = new Database();
$pdo = $database->connect();

