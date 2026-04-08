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
    
    // Recuperando informações do Banco de Dados
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

        // Segunda situação - RECEBENDO O ID por GET
        $stmt = $conexao->prepare("DELETE FROM funcionarios WHERE id=?");
        $stmt->bind_param("i",$id);
        $stmt->execute();
      
        if($stmt->affected_rows > 0){
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