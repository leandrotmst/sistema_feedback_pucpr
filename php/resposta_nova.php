<?php
/**
 * Nova resposta — delega para RespostaModel::novo().
 */
require_once __DIR__ . '/resposta.php';
require_once __DIR__ . '/gestor.php';

$api = new RespostaModel();
Gestor::enviarJson($api->novo());
