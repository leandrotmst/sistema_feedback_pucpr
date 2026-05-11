<?php
session_start();
include_once('../../../php/conexao.php');

$retorno = ['status' => 'nok', 'mensagem' => ''];

if (!isset($_SESSION['admin_id'])) {
    $retorno['mensagem'] = 'Acesso não autorizado';
    echo json_encode($retorno);
    exit;
}

$id = $_POST['id'] ?? '';
$texto_pergunta = $_POST['texto_pergunta'] ?? '';
$tipo_campo = $_POST['tipo_campo'] ?? 'text';
$opcoes = $_POST['opcoes'] ?? null;
$equipe_alvo = $_POST['equipe_alvo'] ?? 'Todas';
$ativa = isset($_POST['ativa']) ? (int)$_POST['ativa'] : 1;

if (empty($id) || empty($texto_pergunta)) {
    $retorno['mensagem'] = 'Preencha o ID e o texto da pergunta';
    echo json_encode($retorno);
    exit;
}

$stmt = $conexao->prepare("UPDATE perguntas SET texto_pergunta = ?, tipo_campo = ?, opcoes = ?, equipe_alvo = ?, ativa = ? WHERE id = ? AND gestor_id IS NULL");
$stmt->bind_param("ssssii", $texto_pergunta, $tipo_campo, $opcoes, $equipe_alvo, $ativa, $id);

if ($stmt->execute()) {
    $retorno['status'] = 'ok';
    $retorno['mensagem'] = 'Pergunta atualizada com sucesso';
} else {
    $retorno['mensagem'] = 'Erro ao atualizar pergunta';
}

echo json_encode($retorno);
?>
