-- Script SQL pour cacher les stories de démonstration
-- Change le statut de 'approved' à 'rejected' pour les cacher de l'interface publique

-- Option 1: Cacher les 4 premières stories (créées lors du setup initial)
UPDATE success_stories 
SET status = 'rejected' 
WHERE id IN (1, 2, 3, 4);

-- Option 2: Cacher par titre (les stories de démonstration)
UPDATE success_stories 
SET status = 'rejected' 
WHERE title IN (
    'Mon Premier Emploi grâce à AbleLink',
    'De Chômeur à Développeur Web',
    'Reconversion Professionnelle Réussie',
    'Histoire en Attente de Validation'
);

-- Pour afficher à nouveau une story spécifique:
-- UPDATE success_stories SET status = 'approved' WHERE id = <ID>;

-- Pour voir toutes les stories et leurs statuts:
SELECT id, title, author, status, created_at FROM success_stories ORDER BY id;
