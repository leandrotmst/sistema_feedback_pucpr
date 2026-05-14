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
        header("Content-type:application/json;charset:utf-8");
        echo json_encode($retorno);
        exit;
    }

    if(isset($_GET['id'])){
        $id = $_GET['id'];
        $gestorId = $_SESSION['gestor_id'];

        // Verificar se a solução pertence a um analista deste gestor
        $stmtCheck = $conexao->prepare("SELECT s.id FROM solucoes s INNER JOIN analista_dados a ON s.analista_id = a.id WHERE s.id=? AND a.gestor_id=?");
        $stmtCheck->bind_param("ii", $id, $gestorId);
        $stmtCheck->execute();
        $resultCheck = $stmtCheck->get_result();
        if ($resultCheck->num_rows == 0) {
            $retorno = [
                'status'   => 'nok',
                'mensagem' => 'Acesso negado: solução não encontrada para este gestor',
                'data'     => []
            ];
            $stmtCheck->close();
            $conexao->close();
            header("Content-type:application/json;charset:utf-8");
            echo json_encode($retorno);
            exit;
        }
        $stmtCheck->close();

        // Apagar
        $stmt = $conexao->prepare("DELETE FROM solucoes WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        if($stmt->affected_rows > 0){
            $retorno = [
                'status'   => 'ok',
                'mensagem' => 'Solução apagada com sucesso',
                'data'     => []
            ];
        }else{
            $retorno = [
                'status'   => 'nok',
                'mensagem' => 'Falha ao apagar solução',
                'data'     => []
            ];
        }
        $stmt->close();
    }else{
        $retorno = [
            'status'   => 'nok',
            'mensagem' => 'ID não informado',
            'data'     => []
        ];
    }

    $conexao->close();
    header("Content-type:application/json;charset:utf-8");
    echo json_encode($retorno);
