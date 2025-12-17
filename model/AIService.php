<?php

class AIService {
    private $apiKey;
    private $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent';
    private $demoMode = false;
    
    public function __construct() {
        // Charger la clé API depuis la configuration
        require_once __DIR__ . '/../config/ai_config.php';
        
        // Vérifier si le mode démo est activé
        if (defined('DEMO_MODE') && DEMO_MODE === true) {
            $this->demoMode = true;
            return; // Pas besoin de clé API en mode démo
        }
        
        $this->apiKey = AI_API_KEY;
        
        // Vérifier que la clé API est configurée
        if ($this->apiKey === 'VOTRE_CLE_API_GOOGLE_GEMINI_ICI' || empty($this->apiKey)) {
            throw new Exception('⚠️ Clé API non configurée. Veuillez configurer votre clé Google Gemini dans config/ai_config.php ou activer DEMO_MODE');
        }
    }
    
    public function analyzeJobOffer($description) {
        // Mode démo : retourner des données simulées
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

        $response = $this->callGeminiAPI($prompt);
        return $this->parseAnalysisResponse($response);
    }
    
    public function optimizeJobOffer($description) {
        // Mode démo : retourner des données simulées
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

        $response = $this->callGeminiAPI($prompt);
        return $this->parseOptimizationResponse($response);
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
            // Décoder la réponse d'erreur pour plus de détails
            $errorDetails = json_decode($response, true);
            $errorMessage = 'Erreur API (Code ' . $httpCode . ')';
            
            if (isset($errorDetails['error']['message'])) {
                $errorMessage .= ': ' . $errorDetails['error']['message'];
            }
            
            throw new Exception($errorMessage);
        }
        
        $result = json_decode($response, true);
        
        if (!isset($result['candidates'][0]['content']['parts'][0]['text'])) {
            throw new Exception('Réponse API invalide');
        }
        
        return $result['candidates'][0]['content']['parts'][0]['text'];
    }
    
    private function parseAnalysisResponse($response) {
        // Extraire le JSON de la réponse (peut contenir du texte avant/après)
        preg_match('/\{[\s\S]*\}/', $response, $matches);
        
        if (empty($matches)) {
            // Réponse par défaut si le parsing échoue
            return [
                'score' => 70,
                'positives' => ['Structure claire de l\'offre'],
                'improvements' => ['Utiliser un langage plus inclusif'],
                'suggestions' => ['Ajouter des informations sur les aménagements possibles']
            ];
        }
        
        $data = json_decode($matches[0], true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Erreur de parsing de la réponse');
        }
        
        return $data;
    }
    
    private function parseOptimizationResponse($response) {
        // Extraire le JSON de la réponse
        preg_match('/\{[\s\S]*\}/', $response, $matches);
        
        if (empty($matches)) {
            // Réponse par défaut si le parsing échoue
            return [
                'text' => $response,
                'changes' => ['Optimisation effectuée']
            ];
        }
        
        $data = json_decode($matches[0], true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'text' => $response,
                'changes' => []
            ];
        }
        
        return $data;
    }
    
    // Fonctions de démonstration (MODE DÉMO)
    private function getDemoAnalysis($description) {
        // Calculer un score basé sur la longueur et quelques mots-clés
        $score = 65; // Score de base
        
        if (stripos($description, 'inclusif') !== false || stripos($description, 'diversité') !== false) {
            $score += 10;
        }
        if (stripos($description, 'salaire') !== false || stripos($description, 'rémunération') !== false) {
            $score += 5;
        }
        if (strlen($description) > 200) {
            $score += 10;
        }
        if (stripos($description, 'télétravail') !== false || stripos($description, 'remote') !== false) {
            $score += 10;
        }
        
        $score = min($score, 100); // Maximum 100
        
        return [
            'score' => $score,
            'positives' => [
                'La description présente une structure cohérente',
                'Les missions sont identifiables',
                'Le contexte de l\'entreprise est mentionné'
            ],
            'improvements' => [
                'Utiliser un langage plus neutre et inclusif',
                'Préciser les possibilités d\'aménagement du poste',
                'Clarifier davantage les compétences requises vs souhaitées'
            ],
            'suggestions' => [
                'Ajouter une mention explicite sur l\'ouverture à tous les profils',
                'Indiquer les modalités de travail (télétravail, horaires flexibles)',
                'Préciser la fourchette de rémunération pour plus de transparence'
            ]
        ];
    }
    
    private function getDemoOptimization($description) {
        // Version optimisée simulée
        $optimized = "Nous recherchons une personne motivée et talentueuse pour rejoindre notre équipe.\n\n";
        $optimized .= "MISSIONS PRINCIPALES :\n";
        $optimized .= "• Contribuer à la conception et au développement de nos solutions\n";
        $optimized .= "• Collaborer avec les équipes métier et techniques\n";
        $optimized .= "• Participer à l'amélioration continue de nos processus\n\n";
        $optimized .= "PROFIL RECHERCHÉ :\n";
        $optimized .= "• Compétences techniques adaptées au poste\n";
        $optimized .= "• Capacité à travailler en équipe et autonomie\n";
        $optimized .= "• Curiosité et envie d'apprendre\n\n";
        $optimized .= "CE QUE NOUS OFFRONS :\n";
        $optimized .= "• Un environnement de travail inclusif et bienveillant\n";
        $optimized .= "• Possibilités de télétravail et horaires flexibles\n";
        $optimized .= "• Rémunération attractive selon expérience\n";
        $optimized .= "• Aménagements possibles selon les besoins individuels\n\n";
        $optimized .= "Toutes les candidatures sont étudiées avec attention, indépendamment du genre, de l'âge, de l'origine ou du handicap.";
        
        return [
            'text' => $optimized,
            'changes' => [
                'Utilisation d\'un langage neutre et inclusif',
                'Structure claire avec sections bien définies',
                'Ajout de mentions sur les aménagements possibles',
                'Mise en avant de la transparence et de l\'inclusivité'
            ]
        ];
    }
}
