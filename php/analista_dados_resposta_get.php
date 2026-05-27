<?php
    session_start();
    include_once('conexao.php');
    // Configurando o padrão de retorno em todas
    // as situações
    $retorno = [
        'status'   => '', // ok - nok
        'mensagem' => '', // mensagem que envio para o front
        'data'     => []
    ];

    if (!isset($_SESSION['id_analista_dados'])) {
        $retorno = [
            'status'   => 'nok',
            'mensagem' => 'Sessão inválida',
            'data'     => []
        ];
        header("Content-type:application/json;charset:utf-8");
        echo json_encode($retorno);
        exit;
    }

    $analistaId = $_SESSION['id_analista_dados'];

    if(isset($_GET['id'])){
        $stmt = $conexao->prepare(
            "SELECT r.texto, r.emocional, f.equipe, r.email_do_funcionario
             FROM respostas r
             JOIN funcionarios f ON f.id = r.funcionarios_id
             JOIN analista_dados a ON a.gestor_id = f.gestor_id
             WHERE r.id = ? AND a.id = ?"
        );
        $stmt->bind_param("ii", $_GET['id'], $analistaId);
    }else{
        $stmt = $conexao->prepare(
            "SELECT r.texto, r.emocional, f.equipe, r.email_do_funcionario
             FROM respostas r
             JOIN funcionarios f ON f.id = r.funcionarios_id
             JOIN analista_dados a ON a.gestor_id = f.gestor_id
             WHERE a.id = ?"
        );
        $stmt->bind_param("i", $analistaId);
    }    
    
    // Recuperando informações do Banco de Dados
    // Vou executar a query
    $stmt->execute();
    $resultado = $stmt->get_result();
    // Criando um array vazio para receber o resultado
    // do banco de Dados
    $tabela = [];
    if($resultado->num_rows > 0){
        while($linha = $resultado->fetch_assoc()){
            $tabela[] = $linha;
        }
        $retorno = [
            'status'   => 'ok', // ok - nok
            'mensagem' => 'Sucesso consulta efetuada', // mensagem que envio para o front
            'data'     => $tabela
        ];
    }else{
        $retorno = [
            'status'   => 'nok', // ok - nok
            'mensagem' => 'Não há registros', // mensagem que envio para o front
            'data'     => []
        ];
    }
    // Fechamento do estado e conexão
    $stmt->close();
    $conexao->close();
    
    // Estou enviando para o FRONT o array RETORNO
    // mas no formato JSON
    header("Content-type:application/json;charset:utf-8");
    echo json_encode($retorno);