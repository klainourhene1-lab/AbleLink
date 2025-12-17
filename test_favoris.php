<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Favoris</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #1a1a2e; color: #fff; }
        .section { background: #16213e; padding: 20px; margin: 20px 0; border-radius: 10px; }
        .success { color: #10b981; }
        .error { color: #ef4444; }
        button { padding: 10px 20px; background: #8b5cf6; color: #fff; border: none; border-radius: 5px; cursor: pointer; }
    </style>
</head>
<body>
    <h1>🔧 Test du Système de Favoris</h1>
    
    <div class="section">
        <h2>1. Vérification de la Session</h2>
        <?php if (isset($_SESSION['user_id'])): ?>
            <p class="success">✅ Utilisateur connecté (ID: <?= $_SESSION['user_id'] ?>)</p>
            <p>Email: <?= $_SESSION['email'] ?? 'Non défini' ?></p>
        <?php else: ?>
            <p class="error">❌ Aucun utilisateur connecté</p>
            <p>Vous devez vous connecter pour utiliser les favoris</p>
            <a href="/projetttwebbbbbbbbb/index.php?controller=auth&action=login" style="color: #8b5cf6;">Se connecter</a>
        <?php endif; ?>
    </div>
    
    <div class="section">
        <h2>2. Vérification de la Table favoris</h2>
        <?php
        try {
            require_once __DIR__ . '/config/database.php';
            
            // Vérifier si la table existe
            $stmt = $pdo->query("SHOW TABLES LIKE 'favoris'");
            $tableExists = $stmt->rowCount() > 0;
            
            if ($tableExists) {
                echo '<p class="success">✅ Table "favoris" existe</p>';
                
                // Compter les favoris
                $stmt = $pdo->query("SELECT COUNT(*) FROM favoris");
                $count = $stmt->fetchColumn();
                echo "<p>Nombre total de favoris: <strong>$count</strong></p>";
                
                // Structure de la table
                $stmt = $pdo->query("DESCRIBE favoris");
                $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo '<details><summary>Structure de la table</summary><pre>';
                foreach ($columns as $col) {
                    echo $col['Field'] . ' - ' . $col['Type'] . "\n";
                }
                echo '</pre></details>';
            } else {
                echo '<p class="error">❌ Table "favoris" n\'existe PAS</p>';
                echo '<p>Vous devez créer la table. Exécutez ce SQL dans phpMyAdmin:</p>';
                echo '<pre style="background: #0f0f23; padding: 15px; border-radius: 5px; overflow-x: auto;">';
                echo htmlspecialchars(file_get_contents(__DIR__ . '/database/favoris.sql'));
                echo '</pre>';
            }
        } catch (Exception $e) {
            echo '<p class="error">❌ Erreur: ' . $e->getMessage() . '</p>';
        }
        ?>
    </div>
    
    <div class="section">
        <h2>3. Test d'Ajout de Favori</h2>
        <p>Cliquez pour tester l'ajout de l'offre #1 aux favoris:</p>
        <button onclick="testAjouterFavori(1)">Tester Ajouter</button>
        <button onclick="testSupprimerFavori(1)" style="background: #ef4444;">Tester Supprimer</button>
        <div id="testResult" style="margin-top: 15px;"></div>
    </div>
    
    <div class="section">
        <h2>4. Test des Endpoints</h2>
        <button onclick="testEndpoints()">Tester tous les endpoints</button>
        <div id="endpointResults" style="margin-top: 15px;"></div>
    </div>
    
    <script>
    function testAjouterFavori(id_offre) {
        const resultDiv = document.getElementById('testResult');
        resultDiv.innerHTML = '<p>⏳ Test en cours...</p>';
        
        fetch('/projetttwebbbbbbbbb/index.php?controller=favoris&action=ajouter', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id_offre: id_offre })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                resultDiv.innerHTML = '<p class="success">✅ ' + data.message + '</p>';
            } else {
                resultDiv.innerHTML = '<p class="error">❌ ' + data.error + '</p>';
            }
            resultDiv.innerHTML += '<pre>' + JSON.stringify(data, null, 2) + '</pre>';
        })
        .catch(error => {
            resultDiv.innerHTML = '<p class="error">❌ Erreur: ' + error.message + '</p>';
            console.error('Erreur:', error);
        });
    }
    
    function testSupprimerFavori(id_offre) {
        const resultDiv = document.getElementById('testResult');
        resultDiv.innerHTML = '<p>⏳ Test en cours...</p>';
        
        fetch('/projetttwebbbbbbbbb/index.php?controller=favoris&action=supprimer', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id_offre: id_offre })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                resultDiv.innerHTML = '<p class="success">✅ ' + data.message + '</p>';
            } else {
                resultDiv.innerHTML = '<p class="error">❌ ' + data.error + '</p>';
            }
            resultDiv.innerHTML += '<pre>' + JSON.stringify(data, null, 2) + '</pre>';
        })
        .catch(error => {
            resultDiv.innerHTML = '<p class="error">❌ Erreur: ' + error.message + '</p>';
            console.error('Erreur:', error);
        });
    }
    
    function testEndpoints() {
        const resultDiv = document.getElementById('endpointResults');
        resultDiv.innerHTML = '<p>⏳ Test en cours...</p>';
        
        let results = '<h3>Résultats:</h3>';
        
        // Test 1: Vérifier
        fetch('/projetttwebbbbbbbbb/index.php?controller=favoris&action=verifier&id_offre=1')
        .then(r => r.json())
        .then(data => {
            results += '<p>✅ Endpoint verifier: ' + JSON.stringify(data) + '</p>';
            resultDiv.innerHTML = results;
        })
        .catch(e => {
            results += '<p class="error">❌ Endpoint verifier: ' + e.message + '</p>';
            resultDiv.innerHTML = results;
        });
    }
    </script>
</body>
</html>
