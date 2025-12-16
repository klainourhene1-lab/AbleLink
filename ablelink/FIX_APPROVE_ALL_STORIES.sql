-- Script SQL pour rendre TOUTES les stories visibles
-- à exécuter dans phpMyAdmin ou via ligne de commande

-- Approuver TOUTES les stories
UPDATE success_stories SET status = 'approved';

-- Vérifier que ça a fonctionné
SELECT id, title, author, status FROM success_stories;
