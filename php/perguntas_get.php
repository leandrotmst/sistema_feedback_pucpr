<?php
session_start();
include_once('conexao.php');

$retorno = [
    'status'   => '',
    'mensagem' => '',
    'data'     => []
];

// Devemos ter o id do funcionário logado
if (!isset($_SESSION['id_funcionario'])) {
    $retorno['status'] = 'nok';
    $retorno['mensagem'] = 'Sessão de funcionário inválida';
    header("Content-type:application/json;charset:utf-8");
    echo json_encode($retorno);
    exit;
}

$equipe = $_SESSION['equipe_funcionario'] ?? 'Todas'; // 'Todas' como fallback seguro
$gestor_id = $_SESSION['gestor_id_funcionario'] ?? 0;

// Buscar as perguntas que sejam globais (gestor_id IS NULL) ou direcionadas para o gestor específico
// E que sejam 'Todas' ou direcionadas especificamente para a equipe do funcionário
$sql = "SELECT id, texto_pergunta, tipo_campo, opcoes FROM perguntas 
        WHERE ativa = 1 
        AND (gestor_id IS NULL OR gestor_id = ?)
        AND (equipe_alvo = 'Todas' OR equipe_alvo = ?)";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("is", $gestor_id, $equipe);
$stmt->execute();
$resultado = $stmt->get_result();

$perguntas = [];
if ($resultado->num_rows > 0) {
    while ($linha = $resultado->fetch_assoc()) {
        $perguntas[] = $linha;
    }
}

$stmt->close();
$conexao->close();

$retorno['status'] = 'ok';
$retorno['mensagem'] = 'Sucesso';
$retorno['data'] = $perguntas;

header("Content-type:application/json;charset:utf-8");
echo json_encode($retorno);
?>
