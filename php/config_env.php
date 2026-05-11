<?php
// Carregador simples de .env para não precisarmos do Composer
function carregarEnv($caminhoArquivo) {
    if (!file_exists($caminhoArquivo)) {
        return false;
    }

    $linhas = file($caminhoArquivo, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($linhas as $linha) {
        // Ignora comentários
        if (strpos(trim($linha), '#') === 0) {
            continue;
        }

        list($nome, $valor) = explode('=', $linha, 2);
        $nome = trim($nome);
        $valor = trim($valor);

        if (!array_key_exists($nome, $_SERVER) && !array_key_exists($nome, $_ENV)) {
            putenv(sprintf('%s=%s', $nome, $valor));
            $_ENV[$nome] = $valor;
            $_SERVER[$nome] = $valor;
        }
    }
    return true;
}

// Inicializar na raiz do projeto (como este script está na pasta php/, o .env está um nível acima)
$caminhoEnv = __DIR__ . '/../.env';
carregarEnv($caminhoEnv);
?>
