<?php
require_once __DIR__ . '/Control/config.php';

echo "Setting up database tables for Job Board Integration...\n";

try {
    $db = Config::getConnexion();
    
    // 1. Table Offres
    $sql_offres = "CREATE TABLE IF NOT EXISTS offres (
        id INT AUTO_INCREMENT PRIMARY KEY,
        titre VARCHAR(255) NOT NULL,
        description TEXT NOT NULL,
        entreprise VARCHAR(255) NOT NULL,
        localisation VARCHAR(255) NOT NULL,
        type_contrat VARCHAR(50) NOT NULL,
        salaire VARCHAR(100),
        date_publication TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    $db->exec($sql_offres);
    echo "Table 'offres' created or already exists.\n";

    // 2. Table Candidatures
    $sql_candidatures = "CREATE TABLE IF NOT EXISTS candidatures (
        id INT AUTO_INCREMENT PRIMARY KEY,
        id_offre INT NOT NULL,
        nom_candidat VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        cv VARCHAR(255),
        statut VARCHAR(50) DEFAULT 'en_attente',
        date_candidature TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (id_offre) REFERENCES offres(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    $db->exec($sql_candidatures);
    echo "Table 'candidatures' created or already exists.\n";

    // 3. Table Favoris
    $sql_favoris = "CREATE TABLE IF NOT EXISTS favoris (
        id_user INT NOT NULL,
        id_offre INT NOT NULL,
        date_ajout TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id_user, id_offre),
        FOREIGN KEY (id_offre) REFERENCES offres(id) ON DELETE CASCADE,
        FOREIGN KEY (id_user) REFERENCES utilisateur(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    $db->exec($sql_favoris);
    echo "Table 'favoris' created or already exists.\n";

    // 4. Table AI Analyses
    $sql_ai = "CREATE TABLE IF NOT EXISTS ai_analyses (
        id INT(11) PRIMARY KEY AUTO_INCREMENT,
        id_offre INT(11) DEFAULT NULL,
        type ENUM('analyze', 'optimize') NOT NULL COMMENT 'Type d''opération: analyze ou optimize',
        input_text TEXT NOT NULL COMMENT 'Texte original soumis à l''IA',
        output_result TEXT NOT NULL COMMENT 'Résultat de l''IA (JSON)',
        score INT(3) DEFAULT NULL COMMENT 'Score d''inclusivité (pour les analyses)',
        date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (id_offre) REFERENCES offres(id) ON DELETE SET NULL ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    $db->exec($sql_ai);
    echo "Table 'ai_analyses' created or already exists.\n";
    
    echo "Database setup completed successfully.\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
