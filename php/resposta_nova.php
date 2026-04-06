<?php
    include_once('conexao.php');
    $retorno = [
        'status'   => '',
        'mensagem' => '',
        'data'     => []
    ];

    $emocional    = $_POST['nivel'];
    $texto    = $_POST['texto'];
    $emailFuncionario = $_POST['email_do_funcionario'];

    // Preparando para inserção no banco de dados
    $stmt = $conexao->prepare("INSERT INTO respostas(emocional, texto, email_do_funcionario) VALUES(?,?,?)");
    $stmt->bind_param("iss", $emocional, $texto, $emailFuncionario);
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