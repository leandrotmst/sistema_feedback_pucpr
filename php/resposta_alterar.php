<?php
    session_start();
    include_once('conexao.php');
    $retorno = [
        'status'   => '',
        'mensagem' => '',
        'data'     => [],
    ];

    if (!isset($_SESSION['email_funcionario'])) {
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
        $emailSessao = $_SESSION['email_funcionario'];

        // First, check if the response belongs to the logged-in user
        $stmtCheck = $conexao->prepare("SELECT id FROM respostas WHERE id=? AND email_do_funcionario=?");
        $stmtCheck->bind_param("is", $id, $emailSessao);
        $stmtCheck->execute();
        $resultCheck = $stmtCheck->get_result();
        if ($resultCheck->num_rows == 0) {
            $retorno = [
                'status'   => 'nok',
                'mensagem' => 'Acesso negado: resposta não pertence ao usuário',
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
        $emocional    = $_POST['emocional'];
        $texto    = $_POST['texto'];
    
        // Preparando para atualização no banco de dados
        $stmt = $conexao->prepare("UPDATE respostas SET emocional=?, texto=? WHERE id=?");
        $stmt->bind_param("isi", $emocional, $texto, $id);
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