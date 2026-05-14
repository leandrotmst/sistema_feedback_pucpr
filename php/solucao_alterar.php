<?php
    session_start();
    include_once('conexao.php');
    $retorno = [
        'status'   => '',
        'mensagem' => '',
        'data'     => [],
    ];

    if (!isset($_SESSION['id_analista_dados'])) {
        $retorno = [
            'status'   => 'nok',
            'mensagem' => 'Sessão inválida',
            'data'     => []
        ];
        header("Content-type:application/json;charset=utf-8");
        echo json_encode($retorno);
        exit;
    }

    if(isset($_GET['id'])){
        $id = $_GET['id'];
        $analistaId = $_SESSION['id_analista_dados'];

        $stmtCheck = $conexao->prepare("SELECT id FROM solucoes WHERE id=? AND analista_id=?");
        $stmtCheck->bind_param("ii", $id, $analistaId);
        $stmtCheck->execute();
        $resultCheck = $stmtCheck->get_result();
        if ($resultCheck->num_rows == 0) {
            $retorno = [
                'status'   => 'nok',
                'mensagem' => 'Acesso negado: solução não pertence a este analista',
                'data'     => []
            ];
            $stmtCheck->close();
            $conexao->close();
            header("Content-type:application/json;charset=utf-8");
            echo json_encode($retorno);
            exit;
        }
        $stmtCheck->close();

        $titulo    = $_POST['titulo'] ?? '';
        $equipe    = $_POST['equipe'] ?? '';
        $descricao = $_POST['descricao'] ?? '';

        if (empty($titulo) || empty($equipe) || empty($descricao)) {
            $retorno = [
                'status'   => 'nok',
                'mensagem' => 'Todos os campos são obrigatórios.',
                'data'     => []
            ];
            header("Content-type:application/json;charset=utf-8");
            echo json_encode($retorno);
            exit;
        }
    
        $stmt = $conexao->prepare("UPDATE solucoes SET titulo=?, equipe=?, descricao=? WHERE id=?");
        $stmt->bind_param("sssi", $titulo, $equipe, $descricao, $id);
        $stmt->execute();

        if($stmt->affected_rows > 0 || $stmt->errno === 0){
            $retorno = [
                'status'   => 'ok',
                'mensagem' => 'Solução alterada com sucesso!',
                'data'     => []
            ];
        }else{
            $retorno = [
                'status'   => 'nok',
                'mensagem' => 'Não foi possível alterar a solução',
                'data'     => []
            ];
        }
        
        $stmt->close();
    }else{
        $retorno = [
            'status'   => 'nok',
            'mensagem' => "Não posso alterar sem um ID informado",
            'data'     => []
        ];
    }
    $conexao->close();

    header("Content-type:application/json;charset=utf-8");
    echo json_encode($retorno);
