<?php
/**
 * Conexão mysqli procedural (igual ao conexao.php do projeto de referência).
 * Ajuste servidor/usuário/senha/banco conforme seu ambiente (ex.: XAMPP).
 */
$servidor   = 'localhost';
$usuario    = 'root';
$senha      = '';
$nome_banco = 'AlignUp';

$conexao = new mysqli($servidor, $usuario, $senha, $nome_banco);
if($conexao->connect_error){
    echo $conexao->connect_error;
}