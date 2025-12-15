<?php
session_start();

function autoTranslate($text, $targetLang) {

    // اذا ترجمتها قبل نحطوها من cache
    if (isset($_SESSION['cache'][$targetLang][$text])) {
        return $_SESSION['cache'][$targetLang][$text];
    }

    $apiKey = "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"; // حط مفتاحك هنا 🔥

    $url = "https://api.openai.com/v1/chat/completions";

    $data = [
        "model" => "gpt-4o-mini",
        "messages" => [
            ["role" => "system", "content" => "Translate the following text to $targetLang"],
            ["role" => "user", "content" => $text]
        ]
    ];

    $options = [
        "http" => [
            "header" => "Content-Type: application/json\r\n" .
                        "Authorization: Bearer $apiKey\r\n",
            "method" => "POST",
            "content" => json_encode($data)
        ]
    ];

    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);

    $translated = json_decode($response, true)['choices'][0]['message']['content'];

    // حفظ الترجمة باش ما يعاودش يترجم
    $_SESSION['cache'][$targetLang][$text] = $translated;

    return $translated;
}
