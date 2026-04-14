<?php
    session_start();
    include_once('conexao.php');
    $retorno = [
        'status'   => '',
        'mensagem' => '',
        'data'     => []
    ];

    if (!isset($_SESSION['email_funcionario'])) {
        $retorno = [
            'status'   => 'nok',
            'mensagem' => 'Sessão inválida',
            'data'     => []
        ];
        header("Content-type:application/json; charset=utf-8");
        echo json_encode($retorno);
        exit;
    }

    $emocional    = $_POST['nivel'];
    $texto    = $_POST['texto'];
    $emailFuncionario = $_SESSION['email_funcionario'];
    $equipeFuncionario = $_SESSION['equipe_funcionario'];
    $funcionarioId = $_SESSION['id_funcionario'];

    // Validação: verificar se id_funcionario foi armazenado na sessão
    if (!isset($funcionarioId) || is_null($funcionarioId)) {
        $retorno = [
            'status'   => 'nok',
            'mensagem' => 'ID do funcionário não foi armazenado. Faça login novamente.',
            'data'     => []
        ];
        header("Content-type:application/json; charset=utf-8");
        echo json_encode($retorno);
        exit;
    }

    // Preparando para inserção no banco de dados
    $stmt = $conexao->prepare("INSERT INTO respostas(emocional, texto, email_do_funcionario, equipe_do_funcionario, funcionarios_id) VALUES(?,?,?,?,?)");
    $stmt->bind_param("isssi", $emocional, $texto, $emailFuncionario, $equipeFuncionario, $funcionarioId);
    $stmt->execute();

    if($stmt->error) {
        $retorno = [
            'status'   => 'nok',
            'mensagem' => 'Erro ao executar: ' . $stmt->error,
            'data'     => []
        ];
    } else if($stmt->affected_rows > 0){
        $retorno = [
            'status'   => 'ok',
            'mensagem' => 'Registro inserido com sucesso',
            'data'     => []
        ];
    }else{
        $retorno = [
            'status'   => 'nok',
            'mensagem' => 'Falha ao inserir o registro',
            'data'     => []
        ];
    }

    $stmt->close();
    $conexao->close();

    header("Content-type:application/json; charset=utf-8");
    echo json_encode($retorno);