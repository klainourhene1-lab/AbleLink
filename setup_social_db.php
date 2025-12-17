<?php
require_once __DIR__ . '/Control/config.php';

try {
    $pdo = Config::getConnexion();

    $sql = "
    CREATE TABLE IF NOT EXISTS `amis` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `idUtilisateur1` int(11) NOT NULL,
        `idUtilisateur2` int(11) NOT NULL,
        `statut` enum('attente', 'accepte', 'refuse') NOT NULL DEFAULT 'attente',
        `date` datetime DEFAULT current_timestamp(),
        PRIMARY KEY (`id`),
        KEY `idUtilisateur1` (`idUtilisateur1`),
        KEY `idUtilisateur2` (`idUtilisateur2`),
        CONSTRAINT `amis_ibfk_1` FOREIGN KEY (`idUtilisateur1`) REFERENCES `utilisateur` (`id`) ON DELETE CASCADE,
        CONSTRAINT `amis_ibfk_2` FOREIGN KEY (`idUtilisateur2`) REFERENCES `utilisateur` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
    ";

    $pdo->exec($sql);
    echo "Table 'amis' created or already exists successfully.<br>";

    // Verify other social tables exist (Post, Commentaire, Reaction) based on projet.sql
    // We assume they might exist from the dump, but let's ensure they are there or create them if missing (basic structure)
    
    // Post
    $sqlPost = "
    CREATE TABLE IF NOT EXISTS `post` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `idUtilisateur` int(11) NOT NULL,
      `titre` varchar(150) DEFAULT NULL,
      `contenu` text DEFAULT NULL,
      `type` varchar(50) DEFAULT NULL,
      `datePublication` datetime DEFAULT NULL,
      `popularite` int(11) DEFAULT 0,
      PRIMARY KEY (`id`),
      KEY `idUtilisateur` (`idUtilisateur`),
      CONSTRAINT `post_ibfk_1` FOREIGN KEY (`idUtilisateur`) REFERENCES `utilisateur` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
    ";
    $pdo->exec($sqlPost);
    echo "Table 'post' checked.<br>";

    // Commentaire
    $sqlComment = "
    CREATE TABLE IF NOT EXISTS `commentaire` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `idUtilisateur` int(11) NOT NULL,
      `idPost` int(11) NOT NULL,
      `contenu` text DEFAULT NULL,
      `dateCommentaire` datetime DEFAULT NULL,
      PRIMARY KEY (`id`),
      KEY `idUtilisateur` (`idUtilisateur`),
      KEY `idPost` (`idPost`),
      CONSTRAINT `commentaire_ibfk_1` FOREIGN KEY (`idUtilisateur`) REFERENCES `utilisateur` (`id`) ON DELETE CASCADE,
      CONSTRAINT `commentaire_ibfk_2` FOREIGN KEY (`idPost`) REFERENCES `post` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
    ";
    $pdo->exec($sqlComment);
    echo "Table 'commentaire' checked.<br>";

    // Reaction
    $sqlReaction = "
    CREATE TABLE IF NOT EXISTS `reaction` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `idUtilisateur` int(11) NOT NULL,
      `idCible` int(11) NOT NULL,
      `type` varchar(50) DEFAULT NULL,
      `dateReaction` datetime DEFAULT NULL,
      PRIMARY KEY (`id`),
      KEY `idUtilisateur` (`idUtilisateur`),
      CONSTRAINT `reaction_ibfk_1` FOREIGN KEY (`idUtilisateur`) REFERENCES `utilisateur` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
    ";
    // Note: Reaction needs to know what it is reacting to. Usually idPost. 
    // The current schema says `idCible` which is generic. Let's stick to that but ensure we handle it in code.
    
    $pdo->exec($sqlReaction);
    echo "Table 'reaction' checked.<br>";

} catch (PDOException $e) {
    die("DB Error: " . $e->getMessage());
}
?>
