<?php
session_start();
header('Content-Type: application/json');
echo json_encode([
    'username' => $_SESSION['username'] ?? '',
    'role' => $_SESSION['role'] ?? ''
]); 