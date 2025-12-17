-- Table pour stocker l'historique des analyses et optimisations de l'Assistant IA
CREATE TABLE IF NOT EXISTS ai_analyses (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    id_offre INT(11) DEFAULT NULL,
    type ENUM('analyze', 'optimize') NOT NULL COMMENT 'Type d''opération: analyze ou optimize',
    input_text TEXT NOT NULL COMMENT 'Texte original soumis à l''IA',
    output_result TEXT NOT NULL COMMENT 'Résultat de l''IA (JSON)',
    score INT(3) DEFAULT NULL COMMENT 'Score d''inclusivité (pour les analyses)',
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    -- Clé étrangère vers la table offres (optionnel)
    FOREIGN KEY (id_offre) REFERENCES offres(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Index pour améliorer les performances
CREATE INDEX idx_id_offre ON ai_analyses(id_offre);
CREATE INDEX idx_type ON ai_analyses(type);
CREATE INDEX idx_date_creation ON ai_analyses(date_creation);
