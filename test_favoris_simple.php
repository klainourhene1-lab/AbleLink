<?php
// Test direct du endpoint favoris
session_start();

echo "<h1>Test Direct Favoris</h1>";
echo "<pre>";

echo "=== ÉTAT DE LA SESSION ===\n";
echo "Session ID: " . session_id() . "\n";
echo "User ID: " . ($_SESSION['user_id'] ?? 'NON DÉFINI') . "\n";
echo "Email: " . ($_SESSION['email'] ?? 'NON DÉFINI') . "\n";
echo "\n";

echo "=== TEST MANUEL ===\n";
if (!isset($_SESSION['user_id'])) {
    echo "❌ PROBLÈME: Vous n'êtes PAS connecté!\n";
    echo "Veuillez vous connecter d'abord ici:\n";
    echo "http://localhost/projetttwebbbbbbbbb/index.php?controller=auth&action=login\n";
} else {
    echo "✅ Vous êtes connecté (User ID: " . $_SESSION['user_id'] . ")\n\n";
    
    // Test de connexion base de données
    echo "=== TEST BASE DE DONNÉES ===\n";
    try {
        require_once __DIR__ . '/model/Database.php';
        $db = Database::getInstance()->getConnection();
        echo "✅ Connexion à la base de données OK\n";
        
        // Vérifier la table favoris
        $stmt = $db->query("SHOW TABLES LIKE 'favoris'");
        if ($stmt->rowCount() > 0) {
            echo "✅ Table 'favoris' existe\n";
            
            // Tester l'insertion
            echo "\n=== TEST D'INSERTION ===\n";
            try {
                $stmt = $db->prepare("INSERT INTO favoris (id_user, id_offre) VALUES (?, ?)");
                $stmt->execute([$_SESSION['user_id'], 1]);
                echo "✅ Insertion réussie!\n";
                echo "Response JSON: " . json_encode(['success' => true, 'message' => 'Ajouté aux favoris']) . "\n";
                
                // Nettoyer le test
                $db->prepare("DELETE FROM favoris WHERE id_user = ? AND id_offre = ?")->execute([$_SESSION['user_id'], 1]);
                echo "✅ Test nettoyé\n";
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) {
                    echo "⚠️ Déjà en favoris (c'est normal si vous avez déjà testé)\n";
                    echo "Response JSON: " . json_encode(['success' => false, 'error' => 'Déjà dans vos favoris']) . "\n";
                } else {
                    echo "❌ Erreur: " . $e->getMessage() . "\n";
                }
            }
        } else {
            echo "❌ Table 'favoris' n'existe PAS\n";
            echo "Créez-la dans phpMyAdmin!\n";
        }
    } catch (Exception $e) {
        echo "❌ Erreur DB: " . $e->getMessage() . "\n";
    }
}

echo "\n=== APPEL DIRECT AU CONTRÔLEUR ===\n";
echo "URL à tester dans votre navigateur:\n";
echo "http://localhost/projetttwebbbbbbbbb/index.php?controller=favoris&action=verifier&id_offre=1\n";
echo "\n";

echo "</pre>";

if (isset($_SESSION['user_id'])) {
    echo '<button onclick="testAjax()">Tester AJAX</button>';
    echo '<div id="result"></div>';
    echo '<script>
    function testAjax() {
        fetch("/projetttwebbbbbbbbb/index.php?controller=favoris&action=ajouter", {
            method: "POST",
            headers: {"Content-Type": "application/json"},
            body: JSON.stringify({id_offre: 1})
        })
        .then(r => r.text())
        .then(text => {
            document.getElementById("result").innerHTML = "<h3>Réponse brute:</h3><pre>" + text + "</pre>";
            try {
                const json = JSON.parse(text);
                document.getElementById("result").innerHTML += "<h3>JSON parsé:</h3><pre>" + JSON.stringify(json, null, 2) + "</pre>";
            } catch(e) {
                document.getElementById("result").innerHTML += "<h3 style=\"color:red\">Erreur: Ce n\'est pas du JSON!</h3>";
            }
        });
    }
    </script>';
}
?>
