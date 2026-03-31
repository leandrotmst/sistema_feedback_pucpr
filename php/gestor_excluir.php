<?php
/**
 * Endpoint HTTP para excluir — delega para Gestor::excluir().
 */
require_once __DIR__ . '/gestor.php';

$api = new Gestor();
Gestor::enviarJson($api->excluir());
