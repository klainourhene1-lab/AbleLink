<?php
header("Content-Type: text/plain");

// 1) النص اللي كتب المستخدم
$message = $_POST['message'] ?? '';

if (!$message) {
    echo "Message vide.";
    exit();
}

// 2) API KEY متاع OpenAI
    $apiKey = "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"; // حط مفتاحك هنا 🔥

// 3) تجهيز الطلب لـ OpenAI
$data = [
    "model" => "gpt-4.1-mini",
    "messages" => [
        [
            "role" => "system",
            "content" => "Tu es l'assistant AbleLink. 
                Tu aides les utilisateurs à créer un compte, remplir un formulaire, comprendre les champs, 
                résoudre les erreurs, expliquer le CAPTCHA, générer un mot de passe, et rendre la plateforme accessible."
        ],
        [
            "role" => "user",
            "content" => $message
        ]
    ]
];

// 4) CURL request
$ch = curl_init("https://api.openai.com/v1/chat/completions");

curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Bearer $apiKey"
]);

curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// 5) Execute
$result = curl_exec($ch);
curl_close($ch);

// 6) Extract response
$response = json_decode($result, true);

echo $response["choices"][0]["message"]["content"] ?? "Erreur dans la réponse AI.";
