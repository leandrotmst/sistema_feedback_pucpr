<?php
    session_start();
    include_once('conexao.php');
    // Configurando o padrão de retorno em todas
    // as situações
    $retorno = [
        'status'   => '',
        'mensagem' => '',
        'data'     => []
    ];

    if (!isset($_SESSION['email_funcionario'])) {
        $retorno = [
            'status'   => 'nok',
            'mensagem' => 'Sessão inválida',
            'data'     => []
        ];
        header("Content-type:application/json;charset:utf-8");
        echo json_encode($retorno);
        $conexao->close();
        exit;
    }

    if(isset($_GET['id'])){
        $id = $_GET['id'];
        $emailSessao = $_SESSION['email_funcionario'];

        $stmtCheck = $conexao->prepare("SELECT funcionarios_id FROM respostas WHERE id=? AND email_do_funcionario=?");
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

        $funcionarioId = $resultCheck->fetch_assoc()['funcionarios_id'];
        $stmtCheck->close();

        $stmt = $conexao->prepare("DELETE FROM respostas WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        if($stmt->affected_rows > 0){
            $stmtUpdate = $conexao->prepare("UPDATE funcionarios SET pontuacao = GREATEST(pontuacao - 1, 0) WHERE id = ?");
            $stmtUpdate->bind_param("i", $funcionarioId);
            $stmtUpdate->execute();
            $stmtUpdate->close();

            $retorno = [
                'status'   => 'ok',
                'mensagem' => 'Registro excluído',
                'data'     => []
            ];
        }else{
            $retorno = [
                'status'   => 'nok',
                'mensagem' => 'Registro não excluído',
                'data'     => []
            ];
        }

        $stmt->close();
    }else{
        $retorno = [
            'status'   => 'nok',
            'mensagem' => 'É necessário informar um ID para exclusão',
            'data'     => []
        ];
    }
    $conexao->close();

    header("Content-type:application/json;charset:utf-8");
    echo json_encode($retorno);