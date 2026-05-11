<?php
session_start();
include_once('../../../php/conexao.php');

$retorno = ['status' => 'nok', 'mensagem' => ''];

if (!isset($_SESSION['admin_id'])) {
    $retorno['mensagem'] = 'Acesso não autorizado';
    echo json_encode($retorno);
    exit;
}

$texto_pergunta = $_POST['texto_pergunta'] ?? '';
$tipo_campo = $_POST['tipo_campo'] ?? 'text';
$opcoes = $_POST['opcoes'] ?? null;
$equipe_alvo = $_POST['equipe_alvo'] ?? 'Todas';
$ativa = isset($_POST['ativa']) ? (int)$_POST['ativa'] : 1;

if (empty($texto_pergunta)) {
    $retorno['mensagem'] = 'Preencha o texto da pergunta';
    echo json_encode($retorno);
    exit;
}

$stmt = $conexao->prepare("INSERT INTO perguntas (texto_pergunta, tipo_campo, opcoes, equipe_alvo, ativa, gestor_id) VALUES (?, ?, ?, ?, ?, NULL)");
$stmt->bind_param("ssssi", $texto_pergunta, $tipo_campo, $opcoes, $equipe_alvo, $ativa);

if ($stmt->execute()) {
    $retorno['status'] = 'ok';
    $retorno['mensagem'] = 'Pergunta adicionada com sucesso';
} else {
    $retorno['mensagem'] = 'Erro ao adicionar pergunta';
}

echo json_encode($retorno);
?>
