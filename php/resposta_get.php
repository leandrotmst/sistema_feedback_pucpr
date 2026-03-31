<?php
/**
 * Lista respostas — delega para RespostaModel::get().
 */
require_once __DIR__ . '/resposta.php';
require_once __DIR__ . '/gestor.php';

$api = new RespostaModel();
Gestor::enviarJson($api->get());
