<?php
/**
 * Page de test pour l'API Google Gemini
 * Accès: http://localhost/projetttwebbbbbbbbb/test_ai.php
 */

echo "<h1>Test de l'API Google Gemini</h1>";

// 1. Vérifier si cURL est installé
echo "<h2>1. Vérification de cURL</h2>";
if (function_exists('curl_version')) {
    $curlVersion = curl_version();
    echo "✅ cURL est installé (version " . $curlVersion['version'] . ")<br>";
} else {
    echo "❌ cURL n'est PAS installé. Activez-le dans php.ini<br>";
    exit;
}

// 2. Vérifier la configuration de la clé API
echo "<h2>2. Configuration de la clé API</h2>";
require_once __DIR__ . '/config/ai_config.php';
echo "Clé API: " . substr(AI_API_KEY, 0, 10) . "..." . substr(AI_API_KEY, -5) . "<br>";

if (AI_API_KEY === 'VOTRE_CLE_API_GOOGLE_GEMINI_ICI') {
    echo "<p style='color: red; font-weight: bold;'>❌ ERREUR: La clé API n'est pas configurée!</p>";
    echo "<p>Pour configurer:</p>";
    echo "<ol>";
    echo "<li>Visitez: <a href='https://makersuite.google.com/app/apikey' target='_blank'>https://makersuite.google.com/app/apikey</a></li>";
    echo "<li>Créez une clé API (gratuit)</li>";
    echo "<li>Ouvrez <code>config/ai_config.php</code></li>";
    echo "<li>Remplacez <code>VOTRE_CLE_API_GOOGLE_GEMINI_ICI</code> par votre clé</li>";
    echo "</ol>";
    exit;
} else {
    echo "✅ Clé API configurée<br>";
}

// 3. Test de connexion à l'API
echo "<h2>3. Test de connexion à l'API</h2>";
$apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent';

$testData = [
    'contents' => [
        [
            'parts' => [
                ['text' => 'Dis bonjour en français']
            ]
        ]
    ]
];

$ch = curl_init($apiUrl . '?key=' . AI_API_KEY);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($testData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Code HTTP: " . $httpCode . "<br>";

if ($httpCode === 200) {
    echo "<p style='color: green; font-weight: bold;'>✅ Connexion réussie!</p>";
    $result = json_decode($response, true);
    if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
        echo "Réponse de l'IA: <strong>" . $result['candidates'][0]['content']['parts'][0]['text'] . "</strong><br>";
    }
    echo "<p style='color: green;'>🎉 L'Assistant IA est prêt à être utilisé!</p>";
} else {
    echo "<p style='color: red; font-weight: bold;'>❌ Erreur de connexion (Code " . $httpCode . ")</p>";
    $errorData = json_decode($response, true);
    echo "<pre style='background: #f0f0f0; padding: 10px; border-radius: 5px;'>";
    echo "Réponse complète:\n";
    print_r($errorData);
    echo "</pre>";
    
    if (isset($errorData['error']['message'])) {
        echo "<p><strong>Message d'erreur:</strong> " . $errorData['error']['message'] . "</p>";
    }
    
    echo "<h3>Solutions possibles:</h3>";
    echo "<ul>";
    echo "<li>Vérifiez que votre clé API est valide</li>";
    echo "<li>Vérifiez que l'API Gemini est activée sur votre projet Google Cloud</li>";
    echo "<li>Vérifiez votre connexion internet</li>";
    echo "</ul>";
}
?>
