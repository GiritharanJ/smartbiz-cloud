<?php
// config/railway-db.php - Railway-specific database class

// Only define if not already defined
if (!class_exists('RailwayDatabase')) {
    class RailwayDatabase {
        private $pdo;
        
        public function connect() {
            try {
                // Railway provides these environment variables
                $host = getenv('PGHOST') ?: 'localhost';
                $port = intval(getenv('PGPORT') ?: '5432');
                $dbname = getenv('PGDATABASE') ?: 'railway';
                $user = getenv('PGUSER') ?: 'postgres';
                $password = getenv('PGPASSWORD') ?: '';
                
                $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;";
                
                $this->pdo = new PDO($dsn, $user, $password, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_TIMEOUT => 5
                ]);
                
                return $this->pdo;
                
            } catch (PDOException $e) {
                error_log("Railway DB Error: " . $e->getMessage());
                die("Database connection error on Railway.");
            }
        }
    }
}

// Conditionally alias to Database class
if (!class_exists('Database')) {
    class Database extends RailwayDatabase {}
}
?>
