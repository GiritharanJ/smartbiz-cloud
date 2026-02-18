<?php
// config/railway-db.php - Railway-specific database config
class RailwayDatabase {
    private $pdo;
    
    public function connect() {
        try {
            // Railway provides these environment variables automatically
            $host = getenv('PGHOST') ?: 'localhost';
            $port = getenv('PGPORT') ?: '5432';
            $dbname = getenv('PGDATABASE') ?: 'railway';
            $user = getenv('PGUSER') ?: 'postgres';
            $password = getenv('PGPASSWORD') ?: '';
            
            $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;";
            
            $this->pdo = new PDO($dsn, $user, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
            
            return $this->pdo;
            
        } catch (PDOException $e) {
            die("Railway DB Connection failed: " . $e->getMessage());
        }
    }
}
?>
