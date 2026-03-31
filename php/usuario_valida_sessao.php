<?php
/**
 * Endpoint HTTP para validar sessão — delega para Usuario::validaSessao().
 */
require_once __DIR__ . '/usuario.php';

$api = new Usuario();
Usuario::enviarJson($api->validaSessao());
