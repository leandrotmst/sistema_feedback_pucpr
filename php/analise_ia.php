<?php

require_once 'config_env.php';

class AnalisadorIA {
    
    private $apiKey;

    public function __construct() {
        $this->apiKey = $_ENV['GEMINI_API_KEY'] ?? getenv('GEMINI_API_KEY');
        if (empty($this->apiKey) || $this->apiKey === 'sua_chave_aqui') {
            throw new Exception("API Key do Gemini não configurada no arquivo .env");
        }
    }

    /**
     * Monta o prompt e envia para a IA
     */
    public function analisarRespostasSemanais(array $respostas): array {
        if (empty($respostas)) {
            return $this->mockResult("Sem dados suficientes para análise nesta semana.");
        }

        $dadosJson = json_encode($respostas, JSON_UNESCAPED_UNICODE);

        $prompt = "Você é um Analista Sênior de Recursos Humanos focado no bem-estar corporativo e clima organizacional.
Sua tarefa é analisar o feedback semanal dos funcionários de uma equipe. 
A escala emocional vai de 0 (pior) a 5 (melhor).
Abaixo estão os dados dos últimos 7 dias em JSON:
{$dadosJson}

Responda OBRIGATORIAMENTE APENAS em formato JSON válido, contendo as três chaves exatas abaixo (em português do Brasil). Não adicione nenhuma formatação Markdown fora do bloco JSON.
{
  \"resumo\": \"Um parágrafo resumindo o clima geral da equipe nesta semana com base nos relatos e nas notas emocionais.\",
  \"alertas\": \"Lista de pontos críticos, se houver (ex: funcionários com nota 0 ou 1, ou mencionando forte estresse/burnout). Se não houver, diga que o clima está estável.\",
  \"solucoes_propostas\": \"Recomendações práticas e acionáveis para o gestor e para a empresa visando melhorar os pontos de atenção levantados nesta semana.\"
}";

        return $this->callGemini($prompt);
    }

    // -------------------------------------------------------------------------
    // Chama Gemini e retorna array PHP (já parseado do JSON)
    // -------------------------------------------------------------------------
    private function callGemini(string $prompt): array {
        $raw    = $this->callGeminiRaw($prompt);
        
        // Limpar possíveis formatações markdown do retorno da IA (```json ... ```)
        $raw    = preg_replace('/^```json\s*/i', '', trim($raw));
        $raw    = preg_replace('/\s*```$/i',     '', $raw);
        
        $result = json_decode(trim($raw), true);
        
        // Se a IA não retornou um JSON válido, retorna um mock seguro
        if (!$result || !isset($result['resumo'])) {
            return $this->mockResult("A IA retornou um formato inesperado. Raw: " . substr($raw, 0, 50));
        }
        
        return $result;
    }

    // -------------------------------------------------------------------------
    // cURL para Gemini 2.0 Flash
    // -------------------------------------------------------------------------
    private function callGeminiRaw(string $prompt): string {
        $data = [
            'contents' => [[
                'parts' => [['text' => $prompt]],
            ]],
            'generationConfig' => [
                'temperature' => 0.4, // Menos criativo, mais focado e analítico
                'responseMimeType' => 'application/json'
            ]
        ];

        $ch = curl_init('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=' . $this->apiKey);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_POSTFIELDS     => json_encode($data),
            CURLOPT_TIMEOUT        => 30, // 30 segundos
            CURLOPT_SSL_VERIFYPEER => false // Para rodar liso no XAMPP local
        ]);

        $result   = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if (!$result || $httpCode !== 200) {
            return '{}';
        }

        $response = json_decode($result, true);
        $text     = $response['candidates'][0]['content']['parts'][0]['text'] ?? '{}';

        return $text;
    }

    private function mockResult(string $motivo = ""): array {
        return [
            "resumo" => "Falha ao gerar o resumo com IA. Motivo: $motivo",
            "alertas" => "N/A",
            "solucoes_propostas" => "N/A"
        ];
    }
}
?>
