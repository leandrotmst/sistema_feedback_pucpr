<?php
/**
 * Endpoint HTTP para get — delega para Gestor::get().
 */
require_once __DIR__ . '/gestor.php';

$api = new Gestor();
Gestor::enviarJson($api->get());
