<?php
class Config {
    private static $pdo = null;

    public static function getConnexion() {
        if (!self::$pdo) {
            try {
                self::$pdo = new PDO(
                    'mysql:host=localhost;dbname=ablelink1;charset=utf8',
                    'root',
                    '',
                    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
                );
            } catch (PDOException $e) {
                die("Erreur de connexion: ".$e->getMessage());
            }
        }
        return self::$pdo;
    }
}
?>
