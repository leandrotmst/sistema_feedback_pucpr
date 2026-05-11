<?php
session_start();
include_once('conexao.php');
require_once('analise_ia.php');

$retorno = [
    'status'   => '',
    'mensagem' => '',
    'data'     => null
];

// O Analista de Dados precisa estar logado
if (!isset($_SESSION['analista_dados_id'])) {
    $retorno['status'] = 'nok';
    $retorno['mensagem'] = 'Sessão inválida. Faça login como analista de dados.';
    header("Content-type:application/json;charset:utf-8");
    echo json_encode($retorno);
    exit;
}

$gestorId = $_SESSION['gestor_id'];

// Buscar as respostas dos últimos 7 dias para a equipe deste gestor
$stmt = $conexao->prepare(
    "SELECT r.texto, r.emocional, r.dados_dinamicos, f.equipe, r.criado_em
     FROM respostas r
     JOIN funcionarios f ON f.email = r.email_do_funcionario
     WHERE f.gestor_id = ? AND r.criado_em >= DATE_SUB(NOW(), INTERVAL 7 DAY)
     ORDER BY r.criado_em DESC"
);
$stmt->bind_param("i", $gestorId);
$stmt->execute();
$resultado = $stmt->get_result();

$respostas_semana = [];
if($resultado->num_rows > 0){
    while($linha = $resultado->fetch_assoc()){
        $respostaTratada = [
            'emocional' => $linha['emocional'],
            'resumo_semana' => $linha['texto'],
            'equipe' => $linha['equipe'],
            'data' => $linha['criado_em']
        ];

        // Se houver dados dinâmicos (JSON), anexamos à resposta
        if (!empty($linha['dados_dinamicos'])) {
            $dadosJSON = json_decode($linha['dados_dinamicos'], true);
            if ($dadosJSON) {
                $respostaTratada['respostas_as_perguntas_semanais'] = $dadosJSON;
            }
        }

        $respostas_semana[] = $respostaTratada;
    }
}

$stmt->close();
$conexao->close();

if (empty($respostas_semana)) {
    $retorno['status'] = 'nok';
    $retorno['mensagem'] = 'Não há respostas na última semana para gerar a análise.';
    header("Content-type:application/json;charset:utf-8");
    echo json_encode($retorno);
    exit;
}

try {
    $ia = new AnalisadorIA();
    $analise = $ia->analisarRespostasSemanais($respostas_semana);
    
    $retorno['status'] = 'ok';
    $retorno['mensagem'] = 'Análise gerada com sucesso';
    $retorno['data'] = $analise; // Contém resumo, alertas e solucoes_propostas
} catch (Exception $e) {
    $retorno['status'] = 'nok';
    $retorno['mensagem'] = 'Erro ao conectar com a IA: ' . $e->getMessage();
}

header("Content-type:application/json;charset:utf-8");
echo json_encode($retorno);
?>
