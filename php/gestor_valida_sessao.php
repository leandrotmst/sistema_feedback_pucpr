<?php
/**
 * Endpoint HTTP para validar sessão — delega para Gestor::validaSessao().
 */
require_once __DIR__ . '/gestor.php';

$api = new Gestor();
Gestor::enviarJson($api->validaSessao());
