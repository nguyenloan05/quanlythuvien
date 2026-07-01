<?php

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../services/AdminService.php';

$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        $service = new AdminService();
        $data = $service->getDashboardData();
        
        
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(405);
        echo json_encode(["error" => "Method không hợp lệ"], JSON_UNESCAPED_UNICODE);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Lỗi Server: " . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
?>