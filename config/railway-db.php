<?php
class Database {
    private $pdo;

    public function connect() {

        $host = getenv('PGHOST');
        $port = getenv('PGPORT');
        $dbname = getenv('PGDATABASE');
        $user = getenv('PGUSER');
        $password = getenv('PGPASSWORD');

        if (!$host || !$port || !$dbname || !$user || !$password) {
            die("Railway ENV variables missing.");
        }

        $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";

        $this->pdo = new PDO($dsn, $user, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);

        return $this->pdo;
    }
}

