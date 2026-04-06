<?php
    include_once('conexao.php');
    $retorno = [
        'status'   => '',
        'mensagem' => '',
        'data'     => []
    ];

    // Simulando as informações que vem do front
    $emocional    = $_POST['emocional'];
    $texto    = $_POST['texto'];
    $emailFuncionario = $_POST['email_do_funcionario'];

    // Preparando para inserção no banco de dados
    $stmt = $conexao->prepare("INSERT INTO respostas(emocional, texto, email_do_funcionario) VALUES(?,?,?)");
    $stmt->bind_param("ss", $emocional, $texto);
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