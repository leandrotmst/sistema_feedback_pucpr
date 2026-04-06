<?php
/**
 * Conexão mysqli procedural (igual ao conexao.php do projeto de referência).
 * Ajuste servidor/usuário/senha/banco conforme seu ambiente (ex.: XAMPP).
 */
$servidor   = 'localhost';
$funcionario    = 'root';
$senha      = '';
$nome_banco = 'projeto';

$conexao = new mysqli($servidor, $funcionario, $senha, $nome_banco);
if($conexao->connect_error){
    echo $conexao->connect_error;
}