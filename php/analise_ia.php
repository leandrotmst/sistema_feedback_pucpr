<?php
    // -------------------------------------------------------------------------
    // Chama Gemini e retorna array PHP (já parseado do JSON)
    // -------------------------------------------------------------------------
    private function callGemini(string $prompt): array {
        $raw    = $this->callGeminiRaw($prompt);
        $raw    = preg_replace('/^```json\s*/i', '', trim($raw));
        $raw    = preg_replace('/\s*```$/i',     '', $raw);
        $result = json_decode(trim($raw), true);
        return $result ?: $this->mockResult();
    }

    // -------------------------------------------------------------------------
    // cURL para Gemini 2.0 Flash — igual ao padrão do VetLume
    // -------------------------------------------------------------------------
    private function callGeminiRaw(string $prompt): string {
        $apiKey = GEMINI_API_KEY;

        $data = [
            'contents' => [[
                'parts' => [['text' => $prompt]],
            ]],
        ];

        $ch = curl_init(
            'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=' . $apiKey
        );
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_POSTFIELDS     => json_encode($data),
            CURLOPT_TIMEOUT        => 60,
        ]);

        $result   = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if (!$result || $httpCode !== 200) {
            return '{}';
        }

        $response = json_decode($result, true);
        $text     = $response['candidates'][0]['content']['parts'][0]['text'] ?? '';

        // Limpeza básica de whitespace excessivo (padrão VetLume)
        $text = preg_replace("/[\r\n]{3,}/", "\n\n", $text);
        $text = preg_replace("/[ \t]+/",     " ",    $text);

        return $text;
    }
