<?php
// Script pour générer le hash du mot de passe "admin123"
$password = 'admin123';
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "Mot de passe: $password\n";
echo "Hash: $hash\n";
echo "\n";

// Connexion à la base de données et mise à jour
try {
    $pdo = new PDO('mysql:host=localhost;dbname=ablelink_db;charset=utf8mb4', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Mettre à jour les deux admins avec le nouveau hash
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE role = 'admin'");
    $stmt->execute([$hash]);
    
    echo "✅ Mots de passe admin mis à jour avec succès!\n";
    echo "Email: ahmedmohsen@gmail.com - Password: admin123\n";
    echo "Email: nourbouabid@gmail.com - Password: admin123\n";
    
    // Vérifier
    $stmt = $pdo->query("SELECT id, name, email, role FROM users WHERE role = 'admin'");
    $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "\n📋 Comptes Admin:\n";
    foreach ($admins as $admin) {
        echo "  - {$admin['name']} ({$admin['email']})\n";
    }
    
} catch (PDOException $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}
?>
