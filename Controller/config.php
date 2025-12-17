<?php
require_once __DIR__ . '/../Model/Database.php';

// Config class wrapper for backward compatibility
class Config {
    private static $db = null;
    
    public static function getConnexion() {
        if (self::$db === null) {
            self::$db = Database::getInstance()->getConnection();
        }
        return self::$db;
    }
}
?>