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

    // Preparando para inserção no banco de dados
    $stmt = $conexao->prepare("INSERT INTO respostas(emocional, texto, email_do_funcionario, equipe_do_funcionario) VALUES(?,?,?,?)");
    $stmt->bind_param("isss", $emocional, $texto, $emailFuncionario, $equipeFuncionario);
    $stmt->execute();

    if($stmt->affected_rows > 0){
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