-- Table pour stocker les favoris des utilisateurs
CREATE TABLE IF NOT EXISTS favoris (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    id_user INT(11) NOT NULL,
    id_offre INT(11) NOT NULL,
    date_ajout DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    -- Clés étrangères
    FOREIGN KEY (id_offre) REFERENCES offres(id) ON DELETE CASCADE ON UPDATE CASCADE,
    
    -- Index pour éviter les doublons et optimiser les requêtes
    UNIQUE KEY unique_favoris (id_user, id_offre),
    INDEX idx_user (id_user),
    INDEX idx_offre (id_offre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
