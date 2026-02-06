<?php
class Database {
    private static $instance = null;
    private $connection;

    // Změň si hesla podle potřeby
    private $host = "db.r6.websupport.sk";
    private $db   = "6BG9tIxP";
    private $user = "jNCsZrD8";
    private $pass = 'M#o1;sE8RF]RxB&4bdKj';

    private function __construct() {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->db};charset=utf8mb4";
            $this->connection = new PDO($dsn, $this->user, $this->pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            die("Chyba databáze: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new Database();
        }
        return self::$instance->connection;
    }
}
