<?php
/**
 * Endpoint HTTP para novo — delega para Usuario::novo().
 */
require_once __DIR__ . '/usuario.php';

$api = new Usuario();
Usuario::enviarJson($api->novo());
