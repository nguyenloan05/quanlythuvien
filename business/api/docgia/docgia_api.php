<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . "/../../services/docgia/DocGiaService.php";

$docGiaService = new DocGiaService();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $userId = $_SESSION['user_id'] ?? 0;

    if (!$userId) {
        http_response_code(401);
        echo json_encode([
            "status" => "error",
            "message" => "Bạn chưa đăng nhập."
        ]);
        exit;
    }

    $result = $docGiaService->getFullInfoByUserId($userId);

    if ($result) {
        echo json_encode([
            "status" => "success",
            "data" => $result
        ]);
    } else {
        http_response_code(404);
        echo json_encode([
            "status" => "error",
            "message" => "Không tìm thấy thông tin độc giả."
        ]);
    }
    exit;
}

if ($method === 'POST') {
    $userId = $_SESSION['user_id'] ?? 0;

    if (!$userId) {
        http_response_code(401);
        echo json_encode([
            "status" => "error",
            "message" => "Bạn chưa đăng nhập."
        ]);
        exit;
    }

    $ma_docgia = $_POST['ma_docgia'] ?? null;

    if (!$ma_docgia) {
        http_response_code(400);
        echo json_encode([
            "status" => "error",
            "message" => "Thiếu mã độc giả"
        ]);
        exit;
    }

    $result = $docGiaService->kichHoatThe($_POST);

    echo json_encode($result);
    exit;
}

http_response_code(405);
echo json_encode([
    "status" => "error",
    "message" => "Phương thức không được hỗ trợ"
]);
