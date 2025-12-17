<?php
class Database {
    private $host = "127.0.0.1";
    private $db_name = "projet";
    private $username = "root";
    private $password = "";
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        return $this->conn;
    }
}

// Config class wrapper for backward compatibility
class Config {
    private static $db = null;
    
    public static function getConnexion() {
        if (self::$db === null) {
            $database = new Database();
            self::$db = $database->getConnection();
        }
        return self::$db;
    }
}
?>