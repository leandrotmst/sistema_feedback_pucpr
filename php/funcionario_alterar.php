<?php
    session_start();
    include_once('conexao.php');
    $retorno = [
        'status'   => '',
        'mensagem' => '',
        'data'     => [],
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

        // First, check if the funcionario belongs to the logged-in gestor
        $stmtCheck = $conexao->prepare("SELECT id FROM funcionarios WHERE id=? AND gestor_id=?");
        $stmtCheck->bind_param("ii", $id, $gestorId);
        $stmtCheck->execute();
        $resultCheck = $stmtCheck->get_result();
        if ($resultCheck->num_rows == 0) {
            $retorno = [
                'status'   => 'nok',
                'mensagem' => 'Acesso negado: funcionário não pertence ao gestor',
                'data'     => []
            ];
            $stmtCheck->close();
            $conexao->close();
            header("Content-type:application/json;charset:utf-8");
            echo json_encode($retorno);
            exit;
        }
        $stmtCheck->close();

        // Simulando as informações que vem do front
        $email    = $_POST['email'];
        $senha    = $_POST['senha'];
        $equipe    = $_POST['equipe'];
    
        // Preparando para atualização no banco de dados
        $stmt = $conexao->prepare("UPDATE funcionarios SET email=?, senha=?, equipe=? WHERE id=?");
        $stmt->bind_param("sssi", $email, $senha, $equipe, $id);
        $stmt->execute();

        if($stmt->affected_rows > 0){
            $retorno = [
                'status'   => 'ok',
                'mensagem' => 'Registro alterado com sucesso!',
                'data'     => []
            ];
        }else{
            $retorno = [
                'status'   => 'nok',
                'mensagem' => 'Não foi possível alterar o registro',
                'data'     => []
            ];
        }
        
        $stmt->close();
    }else{
        $retorno = [
            'status'   => 'nok',
            'mensagem' => "Não posso alterar um registro sem um ID informado",
            'data'     => []
        ];
    }
    $conexao->close();

    header("Content-type:application/json;charset:utf-8");
    echo json_encode($retorno);