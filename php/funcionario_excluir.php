<?php
    include_once('conexao.php');
    // Configurando o padrão de retorno em todas
    // as situações
    $retorno = [
        'status'   => '',
        'mensagem' => '',
        'data'     => []
    ];
    
    // Recuperando informações do Banco de Dados
    if(isset($_GET['id'])){
        // Segunda situação - RECEBENDO O ID por GET
        $stmt = $conexao->prepare("DELETE FROM funcionarios WHERE id=?");
        $stmt->bind_param("i",$_GET['id']);
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