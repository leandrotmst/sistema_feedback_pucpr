<?php
session_start();
include_once('../conexao.php');

$retorno = ['status' => 'nok', 'mensagem' => '', 'data' => []];

if (!isset($_SESSION['gestor_id'])) {
    $retorno['mensagem'] = 'Acesso não autorizado';
    echo json_encode($retorno);
    exit;
}

$gestor_id = $_SESSION['gestor_id'];

$sql = "SELECT id, texto_pergunta, tipo_campo, opcoes, equipe_alvo, ativa, criado_em FROM perguntas WHERE gestor_id = ? ORDER BY id DESC";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $gestor_id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    while ($linha = $resultado->fetch_assoc()) {
        $retorno['data'][] = $linha;
    }
}

$retorno['status'] = 'ok';
echo json_encode($retorno);
?>
