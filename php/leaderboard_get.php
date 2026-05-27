<?php
include_once('conexao.php');

$retorno = [
    'status' => 'nok',
    'mensagem' => 'Falha ao carregar leaderboard',
    'data' => []
];

$stmt = $conexao->prepare(
    "SELECT f.id, f.email, f.equipe, f.pontuacao,
        (SELECT COUNT(*) FROM respostas r WHERE r.funcionarios_id = f.id AND YEARWEEK(r.criado_em, 1) = YEARWEEK(NOW(), 1)) AS respostas_semana
    FROM funcionarios f
    WHERE f.pontuacao > 0
    ORDER BY f.pontuacao DESC, respostas_semana DESC, f.email ASC"
);

if ($stmt) {
    $stmt->execute();
    $resultado = $stmt->get_result();
    $tabela = [];

    if ($resultado) {
        while ($linha = $resultado->fetch_assoc()) {
            $linha['pontuacao'] = (int)$linha['pontuacao'];
            $linha['respostas_semana'] = (int)$linha['respostas_semana'];
            $tabela[] = $linha;
        }

        $retorno = [
            'status' => 'ok',
            'mensagem' => 'Leaderboard carregado com sucesso',
            'data' => $tabela
        ];
    }

    $stmt->close();
}

$conexao->close();

header('Content-Type: application/json; charset=utf-8');
echo json_encode($retorno);
