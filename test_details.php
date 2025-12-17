<?php
// Test simple et direct du système de détails
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>";
echo "<html><head><title>Test Détails</title></head><body>";
echo "<h1>Test Direct du Système Voir Détails</h1>";

// 1. Tester le modèle
echo "<h2>1. Test du Modèle</h2>";
require_once __DIR__ . '/model/Database.php';
require_once __DIR__ . '/model/Offre.php';

try {
    $offreModel = new Offre();
    echo "<p style='color: green;'>✅ Connexion au modèle réussie</p>";
    
    // Récupérer toutes les offres pour vérifier
    $offres = $offreModel->getAll();
    echo "<p>Nombre d'offres trouvées: " . count($offres) . "</p>";
    
    if (count($offres) > 0) {
        echo "<h3>Première offre disponible:</h3>";
        $premiere = $offres[0];
        echo "<pre>";
        print_r($premiere);
        echo "</pre>";
        
        // Tester getById avec cette offre
        $id_test = $premiere['id'];
        echo "<h3>Test de getById avec ID = $id_test:</h3>";
        $offre_test = $offreModel->getById($id_test);
        if ($offre_test) {
            echo "<p style='color: green;'>✅ getById fonctionne correctement</p>";
            echo "<pre>";
            print_r($offre_test);
            echo "</pre>";
            
            // Créer un lien de test
            echo "<hr><h2>2. Test du Lien</h2>";
            echo "<p>Cliquez ci-dessous pour tester le vrai bouton 'Voir détails':</p>";
            $url = "/projetttwebbbbbbbbb/index.php?controller=offre&action=details&id=" . $id_test;
            echo "<a href='$url' style='display: inline-block; padding: 12px 30px; background: #3a4ced; color: #fff; text-decoration: none; border-radius: 10px; font-weight: 600;'>";
            echo "🔍 Voir détails de l'offre ID=$id_test";
            echo "</a>";
            
        } else {
            echo "<p style='color: red;'>❌ getById a échoué</p>";
        }
    } else {
        echo"<p style='color: red;'>❌ Aucune offre dans la base de données</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erreur: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

// 3. Vérifier la configuration PHP
echo "<hr><h2>3. Configuration PHP</h2>";
echo "<p>Version PHP: " . phpversion() . "</p>";
echo "<p>display_errors: " . ini_get('display_errors') . "</p>";
echo "<p>error_reporting: " . ini_get('error_reporting') . "</p>";

echo "</body></html>";
?>
