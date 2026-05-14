<?php
session_start();
include_once('../conexao.php');

$retorno = ['status' => 'nok', 'mensagem' => ''];

if (!isset($_SESSION['gestor_id'])) {
    $retorno['mensagem'] = 'Acesso não autorizado';
    echo json_encode($retorno);
    exit;
}

$gestor_id = $_SESSION['gestor_id'];
$id = $_POST['id'] ?? '';

if (empty($id)) {
    $retorno['mensagem'] = 'ID não fornecido';
    echo json_encode($retorno);
    exit;
}

$stmt = $conexao->prepare("DELETE FROM perguntas WHERE id = ? AND gestor_id = ?");
$stmt->bind_param("ii", $id, $gestor_id);

if ($stmt->execute()) {
    $retorno['status'] = 'ok';
    $retorno['mensagem'] = 'Pergunta apagada com sucesso';
} else {
    $retorno['mensagem'] = 'Erro ao apagar pergunta';
}

echo json_encode($retorno);
?>
