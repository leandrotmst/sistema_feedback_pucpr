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

    $emailSessao = $_SESSION['email_funcionario'];

    if(isset($_GET['id'])){
        // Segunda situação - RECEBENDO O ID por GET
        $stmt = $conexao->prepare("SELECT * FROM respostas WHERE id=? AND email_do_funcionario=?");
        $stmt->bind_param("is",$_GET['id'], $emailSessao);
    }else{
        // Primeira situação - SEM RECEBER O ID por GET
        $stmt = $conexao->prepare("SELECT * FROM respostas WHERE email_do_funcionario=?");
        $stmt->bind_param("s", $emailSessao);
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