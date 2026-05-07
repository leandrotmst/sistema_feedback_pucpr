<?php
session_start();
header('Content-Type: application/json');

if (isset($_SESSION['email_analista_dados'])) {
    echo json_encode([
        'status' => 'ok',
        'email' => $_SESSION['email_analista_dados'],
        'id' => $_SESSION['id_analista_dados']
    ]);
} else {
    echo json_encode(['status' => 'error']);
}