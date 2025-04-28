<?php
// Set header untuk response JSON
header('Content-Type: application/json');

// Ambil path dari URL
$request_uri = $_SERVER['REQUEST_URI'];
$base_path = '/EAI/index.php/services/';

// Cek apakah request mengarah ke services
if (strpos($request_uri, $base_path) === 0) {
    $service_path = substr($request_uri, strlen($base_path));
    
    // Routing ke service yang sesuai
    switch ($service_path) {
        case 'customer_inquiries.php':
            require_once 'services/customer_inquiries.php';
            break;
        case 'support_tickets.php':
            require_once 'services/support_tickets.php';
            break;
        case 'user_feedbacks.php':
            require_once 'services/user_feedbacks.php';
            break;
        default:
            http_response_code(404);
            echo json_encode(['error' => 'Service tidak ditemukan']);
    }
    exit;
} else {
    // Jika bukan request ke services, tampilkan informasi API
    echo json_encode([
        'message' => 'Customer Service System API',
        'services' => [
            'customer_inquiries' => '/EAI/services/customer_inquiries.php',
            'support_tickets' => '/EAI/services/support_tickets.php',
            'user_feedbacks' => '/EAI/services/user_feedbacks.php'
        ]
    ]);
} 