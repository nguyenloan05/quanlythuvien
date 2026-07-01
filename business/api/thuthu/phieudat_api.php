<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../services/thuthu/PhieuDatService.php';

$service = new PhieuDatService();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $data = $service->getAll();
    echo json_encode(["success" => true, "data" => $data]);
    exit;
}

if ($method !== 'POST') {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "message" => "Method not allowed"
    ]);
    exit;
}

$action = $_POST['action'] ?? '';
$ma_pdt = $_POST['ma_pdt'] ?? null;

if (!$ma_pdt && $action !== '') {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Thiếu mã phiếu đặt"
    ]);
    exit;
}

switch ($action) {
    case 'approve':
        $result = $service->approve($ma_pdt);
        echo json_encode([
            "success" => $result !== false,
            "message" => $result !== false ? "Duyệt phiếu thành công" : "Duyệt phiếu thất bại"
        ]);
        break;

    case 'reject':
        $result = $service->reject($ma_pdt);
        echo json_encode([
            "success" => $result !== false,
            "message" => $result !== false ? "Từ chối phiếu thành công" : "Từ chối phiếu thất bại"
        ]);
        break;

    case 'success':
        $result = $service->markReceived($ma_pdt);
        echo json_encode([
            "success" => $result !== false,
            "message" => $result !== false ? "Xác nhận nhận sách thành công" : "Xác nhận nhận sách thất bại"
        ]);
        break;

    default:
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "Action không hợp lệ"
        ]);
        break;
}
