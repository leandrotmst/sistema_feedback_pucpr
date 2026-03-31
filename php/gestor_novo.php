<?php
/**
 * Endpoint HTTP para novo — delega para Gestor::novo().
 */
require_once __DIR__ . '/gestor.php';

$api = new Gestor();
Gestor::enviarJson($api->novo());
