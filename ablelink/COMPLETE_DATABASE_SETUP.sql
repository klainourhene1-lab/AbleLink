-- ============================================================
-- ABLELINK - COMPLETE DATABASE SETUP
-- ============================================================
-- Instructions:
-- 1. Ouvrir http://localhost/phpmyadmin
-- 2. Cliquer sur "SQL" dans le menu du haut
-- 3. Copier-coller TOUT ce script
-- 4. Cliquer "Exécuter"
-- ============================================================

-- Créer la base de données
CREATE DATABASE IF NOT EXISTS ablelink_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ablelink_db;

-- ============================================================
-- TABLE: success_stories
-- ============================================================
CREATE TABLE IF NOT EXISTS success_stories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    description TEXT,
    category VARCHAR(100),
    image VARCHAR(255),
    video_url VARCHAR(255),
    likes INT DEFAULT 0,
    shares INT DEFAULT 0,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_author (author),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: comments
-- ============================================================
CREATE TABLE IF NOT EXISTS comments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    story_id INT NOT NULL,
    parent_id INT NULL,
    author VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    comment_text TEXT,
    likes INT DEFAULT 0,
    reported TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (story_id) REFERENCES success_stories(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES comments(id) ON DELETE CASCADE,
    INDEX idx_story_id (story_id),
    INDEX idx_parent_id (parent_id),
    INDEX idx_reported (reported)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- DONNÉES D'EXEMPLE (pour tester)
-- ============================================================

-- Insérer des Success Stories d'exemple (APPROUVÉES)
INSERT INTO success_stories (title, author, content, category, status, likes, shares) VALUES
(
    'Mon Premier Emploi grâce à AbleLink',
    'Ahmed Ben Ali',
    'Après 2 ans de recherche difficile, j''ai enfin trouvé mon premier emploi grâce à AbleLink. L''équipe m''a accompagné dans la préparation de mon CV, m''a aidé à préparer mes entretiens et m''a mis en contact avec des employeurs inclusifs. Aujourd''hui, je travaille dans une entreprise formidable qui valorise la diversité. Je recommande vivement AbleLink à tous ceux qui cherchent des opportunités professionnelles!',
    'Career Growth',
    'approved',
    15,
    5
),
(
    'De Chômeur à Développeur Web',
    'Fatima Khadra',
    'Il y a un an, je cherchais désespérément un emploi dans le domaine de l''informatique. Grâce à la formation et au soutien d''AbleLink, j''ai développé mes compétences en développement web. Aujourd''hui, je suis développeuse full-stack dans une start-up innovante. C''est un véritable rêve devenu réalité! Merci AbleLink pour cette opportunité incroyable.',
    'Technology',
    'approved',
    28,
    12
),
(
    'Reconversion Professionnelle Réussie',
    'Mohamed Salah',
    'À 45 ans, j''ai décidé de changer de carrière. AbleLink m''a accompagné tout au long de ce processus de reconversion. Grâce à leurs conseils et leur réseau, j''ai pu me former dans un nouveau domaine et décrocher un poste qui me passionne vraiment. La reconversion n''est jamais facile, mais avec le bon soutien, tout devient possible!',
    'Career Growth',
    'approved',
    42,
    8
),
(
    'Histoire en Attente de Validation',
    'User Test',
    'Ceci est une histoire en attente d''approbation par l''administrateur. Elle ne sera pas visible sur la page publique tant qu''elle n''est pas approuvée.',
    'Test',
    'pending',
    0,
    0
);

-- Insérer des commentaires d'exemple
INSERT INTO comments (story_id, author, content, parent_id, likes, reported) VALUES
(1, 'Utilisateur 1', 'Bravo Ahmed! C''est une histoire vraiment inspirante. Félicitations pour ta persévérance!', NULL, 5, 0),
(1, 'Utilisateur 2', 'Merci de partager ton expérience, cela m''encourage beaucoup à continuer mes recherches.', NULL, 3, 0),
(1, 'Ahmed Ben Ali', 'Merci beaucoup! N''abandonnez jamais, les bonnes opportunités arrivent!', 2, 2, 0),
(2, 'Developer Friend', 'Super parcours Fatima! Quelle stack technique utilises-tu maintenant?', NULL, 1, 0),
(2, 'Fatima Khadra', 'Merci! Je travaille principalement avec React, Node.js et MongoDB.', 4, 1, 0),
(3, 'Career Coach', 'Quelle belle reconversion! Tu es un exemple pour beaucoup de personnes.', NULL, 8, 0);

-- ============================================================
-- VÉRIFICATION
-- ============================================================
-- Afficher le résumé des tables créées
SELECT 'SUCCESS_STORIES TABLE' AS Info, COUNT(*) AS Total FROM success_stories
UNION ALL
SELECT 'COMMENTS TABLE' AS Info, COUNT(*) AS Total FROM comments
UNION ALL
SELECT 'APPROVED STORIES' AS Info, COUNT(*) AS Total FROM success_stories WHERE status = 'approved'
UNION ALL
SELECT 'PENDING STORIES' AS Info, COUNT(*) AS Total FROM success_stories WHERE status = 'pending';

-- ============================================================
-- ✅ SETUP TERMINÉ!
-- ============================================================
-- Tables créées:
--   ✅ success_stories (avec 4 exemples: 3 approuvées, 1 en attente)
--   ✅ comments (avec 6 exemples)
--
-- Colonnes importantes:
--   • success_stories.status: 'pending'|'approved'|'rejected'
--   • success_stories.shares: nombre de partages
--   • success_stories.content: contenu principal
--   • comments.parent_id: pour les réponses
--   • comments.reported: pour signaler les commentaires
-- ============================================================
