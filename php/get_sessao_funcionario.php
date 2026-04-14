<?php
session_start();
header('Content-Type: application/json');

if (isset($_SESSION['email_funcionario'])) {
    echo json_encode([
        'status' => 'ok',
        'email' => $_SESSION['email_funcionario'],
        'equipe' => $_SESSION['equipe_funcionario'],
        'id' => $_SESSION['id_funcionario']
    ]);
} else {
    echo json_encode(['status' => 'error']);
}