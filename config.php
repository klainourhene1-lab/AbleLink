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
<<<<<<< HEAD
?>
=======
?>
>>>>>>> 8cd957d5fed58dac65bc9e2bb2086919bdd16c84
