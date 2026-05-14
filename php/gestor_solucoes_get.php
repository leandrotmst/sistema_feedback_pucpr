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
        header("Content-type:application/json;charset=utf-8");
        echo json_encode($retorno);
        exit;
    }

    $gestorId = $_SESSION['gestor_id'];

    $query = "SELECT s.id, s.titulo, s.descricao, s.equipe, a.email as analista_email
              FROM solucoes s
              INNER JOIN analista_dados a ON s.analista_id = a.id
              WHERE a.gestor_id = ?";
              
    $stmt = $conexao->prepare($query);
    $stmt->bind_param("i", $gestorId);
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
