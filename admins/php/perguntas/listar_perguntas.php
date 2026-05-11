<?php
session_start();
include_once('../../../php/conexao.php');

$retorno = ['status' => 'nok', 'mensagem' => '', 'data' => []];

if (!isset($_SESSION['admin_id'])) {
    $retorno['mensagem'] = 'Acesso não autorizado';
    echo json_encode($retorno);
    exit;
}

$sql = "SELECT id, texto_pergunta, tipo_campo, opcoes, equipe_alvo, ativa, criado_em FROM perguntas WHERE gestor_id IS NULL ORDER BY id DESC";
$resultado = $conexao->query($sql);

if ($resultado->num_rows > 0) {
    while ($linha = $resultado->fetch_assoc()) {
        $retorno['data'][] = $linha;
    }
}

$retorno['status'] = 'ok';
echo json_encode($retorno);
?>
