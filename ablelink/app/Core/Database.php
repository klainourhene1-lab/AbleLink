<?php
namespace App\Core;

class Database {
    private static ?\PDO $instance = null;
    
    public static function getInstance(): \PDO {
        if (self::$instance === null) {
            $host = 'localhost';
            $dbName = 'ablelink_db';
            $user = 'root';
            $password = '';
            
            try {
                // Try to connect to the database
                self::$instance = new \PDO(
                    "mysql:host=$host;dbname=$dbName;charset=utf8mb4",
                    $user,
                    $password
                );
                self::$instance->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                self::$instance->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
            } catch (\PDOException $e) {
                // If database doesn't exist, try to create it
                if ($e->getCode() == 1049) {
                    try {
                        $pdoTemp = new \PDO("mysql:host=$host;charset=utf8mb4", $user, $password);
                        $pdoTemp->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                        $pdoTemp->exec("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                        
                        self::$instance = new \PDO(
                            "mysql:host=$host;dbname=$dbName;charset=utf8mb4",
                            $user,
                            $password
                        );
                        self::$instance->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                        self::$instance->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
                    } catch (\PDOException $e2) {
                        die("Database connection error: " . $e2->getMessage());
                    }
                } else {
                    die("Database connection error: " . $e->getMessage());
                }
            }
        }
        
        return self::$instance;
    }
}
