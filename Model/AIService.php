<?php
require_once __DIR__ . '/../config/ai_config.php';

class AIService {
    private $apiKey;
    private $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent';
    private $demoMode = false;
    
    public function __construct() {
        if (defined('DEMO_MODE') && DEMO_MODE === true) {
            $this->demoMode = true;
        } else {
             $this->apiKey = defined('AI_API_KEY') ? AI_API_KEY : '';
             if (empty($this->apiKey) || $this->apiKey == 'VOTRE_CLE_API_GOOGLE_GEMINI_ICI') {
                 $this->demoMode = true; // Fallback to demo mode if no key
             }
        }
    }
    
    public function analyzeJobOffer($description) {
        if ($this->demoMode) {
            return $this->getDemoAnalysis($description);
        }
        
        $prompt = "Analyse cette offre d'emploi selon les critères d'inclusivité et de clarté. 

Offre d'emploi:
$description

Fournis une analyse au format JSON avec exactement cette structure:
{
    \"score\": [nombre entre 0 et 100],
    \"positives\": [liste de 2-3 points positifs],
    \"improvements\": [liste de 2-3 points à améliorer],
    \"suggestions\": [liste de 2-3 suggestions concrètes]
}

Critères d'évaluation:
- Langage inclusif (éviter les stéréotypes de genre)
- Clarté des missions et responsabilités
- Accessibilité (mentions d'aménagements possibles)
- Transparence sur la rémunération
- Éviter le jargon excessif";

        try {
            $response = $this->callGeminiAPI($prompt);
            return $this->parseAnalysisResponse($response);
        } catch (Exception $e) {
            return $this->getDemoAnalysis($description); // Fallback on error
        }
    }
    
    public function optimizeJobOffer($description) {
        if ($this->demoMode) {
            return $this->getDemoOptimization($description);
        }
        
        $prompt = "Réécris cette offre d'emploi de manière plus inclusive, claire et attrayante.

Offre d'emploi originale:
$description

Fournis une réponse au format JSON avec exactement cette structure:
{
    \"text\": \"[texte optimisé complet]\",
    \"changes\": [liste de 3-4 modifications principales apportées]
}

Critères d'optimisation:
- Utiliser un langage neutre et inclusif
- Clarifier les missions et compétences requises
- Mentionner les possibilités d'aménagement
- Structurer l'information de manière claire
- Rendre l'offre plus attractive";

        try {
            $response = $this->callGeminiAPI($prompt);
            return $this->parseOptimizationResponse($response);
        } catch (Exception $e) {
             return $this->getDemoOptimization($description); // Fallback on error
        }
    }
    
    private function callGeminiAPI($prompt) {
        $data = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ]
        ];
        
        $ch = curl_init($this->apiUrl . '?key=' . $this->apiKey);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if (curl_errno($ch)) {
            throw new Exception('Erreur de connexion à l\'API: ' . curl_error($ch));
        }
        
        curl_close($ch);
        
        if ($httpCode !== 200) {
            throw new Exception('Erreur API (Code ' . $httpCode . ')');
        }
        
        $result = json_decode($response, true);
        
        if (!isset($result['candidates'][0]['content']['parts'][0]['text'])) {
            throw new Exception('Réponse API invalide');
        }
        
        return $result['candidates'][0]['content']['parts'][0]['text'];
    }
    
    private function parseAnalysisResponse($response) {
        preg_match('/\{[\s\S]*\}/', $response, $matches);
        if (empty($matches)) {
            return $this->getDemoAnalysis("Fallback parsing error");
        }
        $data = json_decode($matches[0], true);
        return $data ?: $this->getDemoAnalysis("Fallback json error");
    }
    
    private function parseOptimizationResponse($response) {
        preg_match('/\{[\s\S]*\}/', $response, $matches);
        if (empty($matches)) {
             return $this->getDemoOptimization("Fallback parsing error");
        }
        $data = json_decode($matches[0], true);
        return $data ?: $this->getDemoOptimization("Fallback json error");
    }
    
    // Fonctions de démonstration (MODE DÉMO)
    private function getDemoAnalysis($description) {
        $score = 65;
        if (stripos($description, 'inclusif') !== false) $score += 10;
        if (stripos($description, 'salaire') !== false) $score += 5;
        $score = min($score, 100);
        return [
            'score' => $score,
            'positives' => ['Structure claire', 'Missions identifiables'],
            'improvements' => ['Langage plus inclusif', 'Possibilités d\'aménagement'],
            'suggestions' => ['Mentionner l\'ouverture à tous profils', 'Préciser la rémunération']
        ];
    }
    
    private function getDemoOptimization($description) {
        return [
            'text' => $description . "\n\n[Version optimisée simulée - Configurer la clé API pour l'IA réelle]",
            'changes' => ['Optimisation simulée']
        ];
    }
}
