<?php
session_start();
header('Content-Type: application/json');

if (isset($_SESSION['gestor_id'])) {
    echo json_encode([
        'status' => 'ok',
        'gestor_id' => $_SESSION['gestor_id']
    ]);
} else {
    echo json_encode(['status' => 'error']);
}