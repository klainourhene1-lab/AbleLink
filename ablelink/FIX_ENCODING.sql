-- ============================================================
-- FIX ENCODING ISSUES - Supprimer et Re-insérer les données
-- ============================================================
-- Ce script corrige les problèmes d'encodage des caractères accentués
-- ============================================================

USE ablelink_db;

-- Supprimer les données d'exemple avec mauvais encodage
DELETE FROM comments WHERE story_id IN (1,2,3,4);
DELETE FROM success_stories WHERE id IN (1,2,3,4);

-- Re-insérer les Success Stories avec le bon encodage UTF-8
INSERT INTO success_stories (id, title, author, content, category, status, likes, shares) VALUES
(
    1,
    'Mon Premier Emploi grâce à AbleLink',
    'Ahmed Ben Ali',
    'Après 2 ans de recherche difficile, j''ai enfin trouvé mon premier emploi grâce à AbleLink. L''équipe m''a accompagné dans la préparation de mon CV, m''a aidé à préparer mes entretiens et m''a mis en contact avec des employeurs inclusifs. Aujourd''hui, je travaille dans une entreprise formidable qui valorise la diversité. Je recommande vivement AbleLink à tous ceux qui cherchent des opportunités professionnelles!',
    'Career Growth',
    'approved',
    15,
    5
),
(
    2,
    'De Chômeur à Développeur Web',
    'Fatima Khadra',
    'Il y a un an, je cherchais désespérément un emploi dans le domaine de l''informatique. Grâce à la formation et au soutien d''AbleLink, j''ai développé mes compétences en développement web. Aujourd''hui, je suis développeuse full-stack dans une start-up innovante. C''est un véritable rêve devenu réalité! Merci AbleLink pour cette opportunité incroyable.',
    'Technology',
    'approved',
    28,
    12
),
(
    3,
    'Reconversion Professionnelle Réussie',
    'Mohamed Salah',
    'À 45 ans, j''ai décidé de changer de carrière. AbleLink m''a accompagné tout au long de ce processus de reconversion. Grâce à leurs conseils et leur réseau, j''ai pu me former dans un nouveau domaine et décrocher un poste qui me passionne vraiment. La reconversion n''est jamais facile, mais avec le bon soutien, tout devient possible!',
    'Career Growth',
    'approved',
    42,
    8
),
(
    4,
    'Histoire en Attente de Validation',
    'User Test',
    'Ceci est une histoire en attente d''approbation par l''administrateur. Elle ne sera pas visible sur la page publique tant qu''elle n''est pas approuvée.',
    'Test',
    'pending',
    0,
    0
);

-- Re-insérer les commentaires avec le bon encodage UTF-8
INSERT INTO comments (id, story_id, author, content, parent_id, likes, reported) VALUES
(1, 1, 'Utilisateur 1', 'Bravo Ahmed! C''est une histoire vraiment inspirante. Félicitations pour ta persévérance!', NULL, 5, 0),
(2, 1, 'Utilisateur 2', 'Merci de partager ton expérience, cela m''encourage beaucoup à continuer mes recherches.', NULL, 3, 0),
(3, 1, 'Ahmed Ben Ali', 'Merci beaucoup! N''abandonnez jamais, les bonnes opportunités arrivent!', 2, 2, 0),
(4, 2, 'Developer Friend', 'Super parcours Fatima! Quelle stack technique utilises-tu maintenant?', NULL, 1, 0),
(5, 2, 'Fatima Khadra', 'Merci! Je travaille principalement avec React, Node.js et MongoDB.', 4, 1, 0),
(6, 3, 'Career Coach', 'Quelle belle reconversion! Tu es un exemple pour beaucoup de personnes.', NULL, 8, 0);

-- Vérification
SELECT id, title, author, status FROM success_stories;
