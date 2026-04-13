<?php
    session_start();
    include_once('conexao.php');
    $retorno = [
        'status'   => '',
        'mensagem' => '',
        'data'     => []
    ];

    if (!isset($_SESSION['gestor_id'])) {
        $retorno = [
            'status'   => 'nok',
            'mensagem' => 'Sessão inválida',
            'data'     => []
        ];
        header("Content-type:application/json; charset=utf-8");
        echo json_encode($retorno);
        exit;
    }

    // Simulando as informações que vem do front
    $email     = $_POST['email'];
    $senha     = $_POST['senha'];
    $id_gestor = $_SESSION['gestor_id'];
    $equipe    = $_POST['equipe'];

    // Verificando se o e-mail já existe no banco de dados
    $stmt_check = $conexao->prepare("SELECT id FROM funcionarios WHERE email = ?");
    $stmt_check->bind_param("s", $email);
    $stmt_check->execute();
    $result = $stmt_check->get_result();

    if($result->num_rows > 0){
        $retorno = [
            'status'   => 'nok',
            'mensagem' => 'Este e-mail já está cadastrado no sistema',
            'data'     => []
        ];
        $stmt_check->close();
        $conexao->close();
        header("Content-type:application/json; charset=utf-8");
        echo json_encode($retorno);
        exit;
    }

    $stmt_check->close();

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