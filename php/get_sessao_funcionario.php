<?php
session_start();
include_once('conexao.php');
header('Content-Type: application/json');

if (isset($_SESSION['email_funcionario']) && isset($_SESSION['id_funcionario'])) {
    $funcionarioId = $_SESSION['id_funcionario'];
    $stmt = $conexao->prepare("SELECT pontuacao FROM funcionarios WHERE id = ?");
    $stmt->bind_param("i", $funcionarioId);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado && $resultado->num_rows > 0) {
        $linha = $resultado->fetch_assoc();
        echo json_encode([
            'status' => 'ok',
            'email' => $_SESSION['email_funcionario'],
            'equipe' => $_SESSION['equipe_funcionario'],
            'id' => $funcionarioId,
            'pontuacao' => (int)$linha['pontuacao']
        ]);
    } else {
        echo json_encode(['status' => 'error', 'mensagem' => 'Funcionário não encontrado']);
    }

    $stmt->close();
    $conexao->close();
} else {
    echo json_encode(['status' => 'error', 'mensagem' => 'Sessão inválida']);
}