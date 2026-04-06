<?php
    include_once('conexao.php');
    $retorno = [
        'status'   => '',
        'mensagem' => '',
        'data'     => []
    ];

    // Simulando as informações que vem do front
    $email    = $_POST['email'];
    $senha    = $_POST['senha'];
    $id_gestor = $_POST['id_gestor'];
    $equipe = $_POST['equipe'];

    // Preparando para inserção no banco de dados
    $stmt = $conexao->prepare("INSERT INTO funcionarios(email, senha, equipe, gestor_id) VALUES(?,?,?,?)");
    $stmt->bind_param("sssi", $email, $senha, $equipe, $id_gestor);
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