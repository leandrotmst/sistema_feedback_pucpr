<?php
/**
 * Endpoint HTTP para login — delega para Gestor::login().
 */
require_once __DIR__ . '/gestor.php';

$api = new Gestor();
Gestor::enviarJson($api->alterar());
