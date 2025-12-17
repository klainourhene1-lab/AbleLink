<?php
require_once __DIR__ . '/../Model/FavorisModel.php';

class FavorisController {
    private $favorisModel;

    public function __construct() {
        $this->favorisModel = new FavorisModel();
    }

    // Toggle favorite (add/remove) - helper for API calls
    public function toggle($userId, $offreId) {
        if ($this->favorisModel->isFavorite($userId, $offreId)) {
            $this->favorisModel->remove($userId, $offreId);
            return ['action' => 'removed', 'success' => true];
        } else {
            if ($this->favorisModel->add($userId, $offreId)) {
                return ['action' => 'added', 'success' => true];
            } else {
                return ['success' => false, 'error' => 'Could not add'];
            }
        }
    }
    
    public function add($userId, $offreId) {
        return $this->favorisModel->add($userId, $offreId);
    }
    
    public function remove($userId, $offreId) {
         return $this->favorisModel->remove($userId, $offreId);
    }

    public function getMyFavorites($userId) {
        return $this->favorisModel->getByUser($userId);
    }
    
    public function isFavorite($userId, $offreId) {
        return $this->favorisModel->isFavorite($userId, $offreId);
    }
}
