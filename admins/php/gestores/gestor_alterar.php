<?php
    include_once('../../../php/conexao.php');
    $retorno = [
        'status'   => '',
        'mensagem' => '',
        'data'     => [],
    ];

    if(isset($_GET['id'])){
        // Simulando as informações que vem do front
        $email        = $_POST['email'] ?? '';
        $senha_atual  = $_POST['senha_atual'] ?? '';
        $senha_nova   = $_POST['senha_nova'] ?? '';
        $id = $_GET['id'];

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
        $bind_types = "si";
        $bind_vars = [&$email, &$id];

        // Se quiser mudar a senha
        if (!empty($senha_nova)) {
            // Verifica a senha atual
            $stmtSenha = $conexao->prepare("SELECT senha FROM gestor WHERE id=?");
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
            $bind_types = "ssi";
            $bind_vars = [&$email, &$senha_nova, &$id];
        }
    
        // Preparando para atualização no banco de dados
        $stmt = $conexao->prepare("UPDATE gestor SET email=? $senha_query WHERE id=?");
        
        // Chamada dinâmica de bind_param
        $stmt->bind_param($bind_types, ...$bind_vars);
        $stmt->execute();

        if($stmt->affected_rows > 0 || $stmt->errno === 0){
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