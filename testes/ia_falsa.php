<?php
// "IA falsa" usada nos testes CT43 e CT44 da Mariana.
// Ela responde no mesmo formato da API da Perplexity, mas sem precisar de internet nem de chave.
//   ?modo=certo  -> responde um JSON válido (dentro de ```json ... ```, como a IA de verdade costuma fazer)
//   ?modo=errado -> responde um texto qualquer, que não é JSON

$modo = $_GET['modo'] ?? 'certo';

if ($modo === 'errado') {
    $conteudo = 'Desculpe, não consegui analisar os dados desta semana.';
} else {
    $conteudo = "```json\n{\"resumo\":\"Clima estável\",\"alertas\":\"Nenhum\",\"solucoes_propostas\":\"Manter rituais\"}\n```";
}

header('Content-Type: application/json');
echo json_encode(['choices' => [['message' => ['content' => $conteudo]]]]);
