<?php
    session_start();
    include_once('conexao.php');
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
        header("Content-type:application/json; charset=utf-8");
        echo json_encode($retorno);
        exit;
    }

    $emocional    = $_POST['nivel'] ?? null;
    $texto    = $_POST['texto'] ?? null;

    $emailFuncionario = $_SESSION['email_funcionario'];
    $equipeFuncionario = $_SESSION['equipe_funcionario'];
    $funcionarioId = $_SESSION['id_funcionario'];

    // Verifica dia da semana: 0=Dom, 1=Seg, 2=Ter, 3=Qua, 4=Qui, 5=Sex, 6=Sáb
    $diaSemana = (int)date('w');
    if (in_array($diaSemana, [1, 2, 3])) { 
        $retorno = [
            'status'   => 'nok',
            'mensagem' => 'Fora do período permitido. O preenchimento só é liberado de quinta-feira a domingo.',
            'data'     => []
        ];
        header("Content-type:application/json; charset=utf-8");
        echo json_encode($retorno);
        exit;
    }

    // Validação: verificar se id_funcionario foi armazenado na sessão
    if (!isset($funcionarioId) || is_null($funcionarioId)) {
        $retorno = [
            'status'   => 'nok',
            'mensagem' => 'ID do funcionário não foi armazenado. Faça login novamente.',
            'data'     => []
        ];
        header("Content-type:application/json; charset=utf-8");
        echo json_encode($retorno);
        exit;
    }

    // Verifica se já preencheu nesta semana (segunda a domingo)
    $stmtCheck = $conexao->prepare("SELECT id FROM respostas WHERE funcionarios_id = ? AND YEARWEEK(criado_em, 1) = YEARWEEK(NOW(), 1)");
    $stmtCheck->bind_param("i", $funcionarioId);
    $stmtCheck->execute();
    $resultCheck = $stmtCheck->get_result();
    if ($resultCheck->num_rows > 0) {
        $retorno = [
            'status'   => 'nok',
            'mensagem' => 'Você já enviou um feedback nesta semana. Caso precise alterar, use a opção de edição na tela de respostas.',
            'data'     => []
        ];
        $stmtCheck->close();
        header("Content-type:application/json; charset=utf-8");
        echo json_encode($retorno);
        exit;
    }
    $stmtCheck->close();

    // Preparando para inserção no banco de dados (agora incluindo dados_dinamicos)
    $stmt = $conexao->prepare("INSERT INTO respostas(emocional, texto, email_do_funcionario, equipe_do_funcionario, funcionarios_id) VALUES(?,?,?,?,?)");
    $stmt->bind_param("isssi", $emocional, $texto, $emailFuncionario, $equipeFuncionario, $funcionarioId);
    $stmt->execute();

    if($stmt->error) {
        $retorno = [
            'status'   => 'nok',
            'mensagem' => 'Erro ao executar: ' . $stmt->error,
            'data'     => []
        ];
    } else if($stmt->affected_rows > 0){
        $retorno = [
            'status'   => 'ok',
            'mensagem' => 'Registro inserido com sucesso',
            'data'     => []
        ];
    }else{
        $retorno = [
            'status'   => 'nok',
            'mensagem' => 'Falha ao inserir o registro',
            'data'     => []
        ];
    }

    $stmt->close();
    $conexao->close();

    header("Content-type:application/json; charset=utf-8");
    echo json_encode($retorno);