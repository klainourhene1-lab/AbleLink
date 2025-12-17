<?php

class AIAssistantController {
    
    public function analyze() {
        header('Content-Type: application/json');
        
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $description = $input['description'] ?? '';
            
            if (empty($description)) {
                echo json_encode(['success' => false, 'error' => 'Description vide']);
                return;
            }
            
            require_once __DIR__ . '/../model/AIService.php';
            $aiService = new AIService();
            $analysis = $aiService->analyzeJobOffer($description);
            
            echo json_encode(['success' => true, 'analysis' => $analysis]);
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }
    
    public function optimize() {
        header('Content-Type: application/json');
        
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $description = $input['description'] ?? '';
            
            if (empty($description)) {
                echo json_encode(['success' => false, 'error' => 'Description vide']);
                return;
            }
            
            require_once __DIR__ . '/../model/AIService.php';
            $aiService = new AIService();
            $optimized = $aiService->optimizeJobOffer($description);
            
            echo json_encode(['success' => true, 'optimized' => $optimized]);
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}
