<?php
class Database {
    private static $instance = null;
    private $connection;

    private function __construct() {
        // V ideálním případě načteno z environmentálních proměnných
        $host = getenv('DB_HOST') ?: "db.r6.websupport.sk";
        $db   = getenv('DB_NAME') ?: "6BG9tIxP";
        $user = getenv('DB_USER') ?: "jNCsZrD8";
        $pass = getenv('DB_PASS') ?: 'M#o1;sE8RF]RxB&4bdKj';

        try {
            $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
            $this->connection = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                // Vypnutí emulace pomáhá proti SQL injection u některých typů útoků
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            // Logování chyby místo vypsání hesla uživateli
            error_log($e->getMessage());
            die("Omlouváme se, došlo k problému s připojením.");
        }
    }

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new Database();
        }
        return self::$instance->connection;
    }
}