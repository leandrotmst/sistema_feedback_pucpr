<?php
    session_start();
    include_once('conexao.php');
    $retorno = [
        'status'   => '',
        'mensagem' => '',
        'data'     => []
    ];

    if (!isset($_SESSION['id_analista_dados'])) {
        $retorno = [
            'status'   => 'nok',
            'mensagem' => 'Sessão inválida',
            'data'     => []
        ];
        header("Content-type:application/json; charset=utf-8");
        echo json_encode($retorno);
        exit;
    }

    $titulo = $_POST['titulo'] ?? '';
    $equipe = $_POST['equipe'] ?? '';
    $descricao = $_POST['descricao'] ?? '';
    $id_analista = $_SESSION['id_analista_dados'];

    if (empty($titulo) || empty($equipe) || empty($descricao)) {
        $retorno = [
            'status'   => 'nok',
            'mensagem' => 'Todos os campos são obrigatórios.',
            'data'     => []
        ];
        header("Content-type:application/json; charset=utf-8");
        echo json_encode($retorno);
        exit;
    }

    // Preparando para inserção no banco de dados
    $stmt = $conexao->prepare("INSERT INTO solucoes(titulo, equipe, descricao, analista_id) VALUES(?,?,?,?)");
    if ($stmt === false) {
        $retorno['status'] = 'nok';
        $retorno['mensagem'] = 'Erro no preparo da query: ' . $conexao->error;
        header("Content-type:application/json; charset=utf-8");
        echo json_encode($retorno);
        exit;
    }

    $stmt->bind_param("sssi", $titulo, $equipe, $descricao, $id_analista);
    $stmt->execute();

    if($stmt->affected_rows > 0){
        $retorno = [
            'status'   => 'ok',
            'mensagem' => 'Solução inserida com sucesso',
            'data'     => []
        ];
    }else{
        $retorno = [
            'status'   => 'nok',
            'mensagem' => 'Falha ao inserir a solução',
            'data'     => []
        ];
    }

    $stmt->close();
    $conexao->close();

    header("Content-type:application/json; charset=utf-8");
    echo json_encode($retorno);
