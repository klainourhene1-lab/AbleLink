<?php

class FavorisController {
    
    public function ajouter() {
        header('Content-Type: application/json');
        session_start();
        
        // Vérifier que l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'error' => 'Vous devez être connecté']);
            return;
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        $id_offre = $input['id_offre'] ?? null;
        
        if (!$id_offre) {
            echo json_encode(['success' => false, 'error' => 'ID offre manquant']);
            return;
        }
        
        try {
            require_once __DIR__ . '/../model/Database.php';
            $db = Database::getInstance()->getConnection();
            
            $stmt = $db->prepare("INSERT INTO favoris (id_user, id_offre) VALUES (?, ?)");
            $stmt->execute([$_SESSION['user_id'], $id_offre]);
            
            echo json_encode(['success' => true, 'message' => 'Ajouté aux favoris']);
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // Duplicate entry
                echo json_encode(['success' => false, 'error' => 'Déjà dans vos favoris']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Erreur: ' . $e->getMessage()]);
            }
        }
    }
    
    public function supprimer() {
        header('Content-Type: application/json');
        session_start();
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'error' => 'Vous devez être connecté']);
            return;
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        $id_offre = $input['id_offre'] ?? null;
        
        if (!$id_offre) {
            echo json_encode(['success' => false, 'error' => 'ID offre manquant']);
            return;
        }
        
        try {
            require_once __DIR__ . '/../model/Database.php';
            $db = Database::getInstance()->getConnection();
            
            $stmt = $db->prepare("DELETE FROM favoris WHERE id_user = ? AND id_offre = ?");
            $stmt->execute([$_SESSION['user_id'], $id_offre]);
            
            echo json_encode(['success' => true, 'message' => 'Retiré des favoris']);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'error' => 'Erreur: ' . $e->getMessage()]);
        }
    }
    
    public function liste() {
        session_start();
        
        if (!isset($_SESSION['user_id'])) {
            header('Location: /projetttwebbbbbbbbb/index.php?controller=auth&action=login');
            exit;
        }
        
        require_once __DIR__ . '/../model/Database.php';
        $db = Database::getInstance()->getConnection();
        
        // Récupérer les offres favorites de l'utilisateur
        $stmt = $db->prepare("
            SELECT o.*, f.date_ajout 
            FROM favoris f
            INNER JOIN offres o ON f.id_offre = o.id
            WHERE f.id_user = ?
            ORDER BY f.date_ajout DESC
        ");
        $stmt->execute([$_SESSION['user_id']]);
        $favoris = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        require_once __DIR__ . '/../view/front/mes_favoris.php';
    }
    
    public function verifier() {
        header('Content-Type: application/json');
        session_start();
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['isFavorite' => false]);
            return;
        }
        
        $id_offre = $_GET['id_offre'] ?? null;
        
        if (!$id_offre) {
            echo json_encode(['isFavorite' => false]);
            return;
        }
        
        try {
            require_once __DIR__ . '/../model/Database.php';
            $db = Database::getInstance()->getConnection();
            
            $stmt = $db->prepare("SELECT COUNT(*) FROM favoris WHERE id_user = ? AND id_offre = ?");
            $stmt->execute([$_SESSION['user_id'], $id_offre]);
            $count = $stmt->fetchColumn();
            
            echo json_encode(['isFavorite' => $count > 0]);
        } catch (PDOException $e) {
            echo json_encode(['isFavorite' => false]);
        }
    }
}
