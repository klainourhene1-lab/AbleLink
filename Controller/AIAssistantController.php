<?php
require_once __DIR__ . '/../Model/AIService.php';

class AIAssistantController {
    
    public function analyze($description) {
        if (empty($description)) {
            return ['success' => false, 'error' => 'Description vide'];
        }
        
        try {
            $aiService = new AIService();
            $analysis = $aiService->analyzeJobOffer($description);
            return ['success' => true, 'analysis' => $analysis];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    public function optimize($description) {
        if (empty($description)) {
            return ['success' => false, 'error' => 'Description vide'];
        }
        
        try {
            $aiService = new AIService();
            $optimized = $aiService->optimizeJobOffer($description);
            return ['success' => true, 'optimized' => $optimized];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
