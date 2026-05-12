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
        $email        = $_POST['email'] ?? '';
        $senha_atual  = $_POST['senha_atual'] ?? '';
        $senha_nova   = $_POST['senha_nova'] ?? '';
        $equipe       = $_POST['equipe'] ?? '';

        if (!strpos($email, '@')) {
            $retorno = [
                'status'   => 'nok',
                'mensagem' => 'E-mail inválido. Deve conter @',
                'data'     => []
            ];
            header("Content-type:application/json;charset:utf-8");
            echo json_encode($retorno);
            exit;
        }

        $senha_query = "";
        $bind_types = "ssi";
        $bind_vars = [&$email, &$equipe, &$id];

        // Se quiser mudar a senha
        if (!empty($senha_nova)) {
            // Verifica a senha atual
            $stmtSenha = $conexao->prepare("SELECT senha FROM funcionarios WHERE id=?");
            $stmtSenha->bind_param("i", $id);
            $stmtSenha->execute();
            $resSenha = $stmtSenha->get_result();
            if ($row = $resSenha->fetch_assoc()) {
                if ($row['senha'] !== $senha_atual) {
                    $retorno = [
                        'status'   => 'nok',
                        'mensagem' => 'A senha atual informada está incorreta.',
                        'data'     => []
                    ];
                    $stmtSenha->close();
                    header("Content-type:application/json;charset:utf-8");
                    echo json_encode($retorno);
                    exit;
                }
            }
            $stmtSenha->close();

            $senha_query = ", senha=?";
            $bind_types = "sssi";
            $bind_vars = [&$email, &$senha_nova, &$equipe, &$id];
        }
    
        // Preparando para atualização no banco de dados
        $stmt = $conexao->prepare("UPDATE funcionarios SET email=?, equipe=? $senha_query WHERE id=?");
        
        // Chamada dinâmica de bind_param
        $stmt->bind_param($bind_types, ...$bind_vars);
        $stmt->execute();

        if($stmt->affected_rows > 0 || $stmt->errno === 0){ // Considera sucesso mesmo se não houver linhas alteradas (caso os dados enviados sejam iguais aos atuais)
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