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
        header("Content-type:application/json;charset=utf-8");
        echo json_encode($retorno);
        exit;
    }

    $analistaId = $_SESSION['id_analista_dados'];

    if(isset($_GET['id'])){
        $stmt = $conexao->prepare("SELECT * FROM solucoes WHERE id=? AND analista_id=?");
        $stmt->bind_param("ii", $_GET['id'], $analistaId);
    }else{
        $stmt = $conexao->prepare("SELECT * FROM solucoes WHERE analista_id=?");
        $stmt->bind_param("i", $analistaId);
    }    
    
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    $tabela = [];
    if($resultado->num_rows > 0){
        while($linha = $resultado->fetch_assoc()){
            $tabela[] = $linha;
        }
        $retorno = [
            'status'   => 'ok',
            'mensagem' => 'Sucesso consulta efetuada',
            'data'     => $tabela
        ];
    }else{
        $retorno = [
            'status'   => 'nok',
            'mensagem' => 'Não há registros',
            'data'     => []
        ];
    }
    
    $stmt->close();
    $conexao->close();
    
    header("Content-type:application/json;charset=utf-8");
    echo json_encode($retorno);
