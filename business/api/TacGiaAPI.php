<?php
require_once __DIR__ . '/../services/TacGiaService.php';
require_once __DIR__ . '/../../data/models/TacGiaModel.php';

header('Content-Type: application/json');
$service = new TacGiaService();
$method  = $_SERVER['REQUEST_METHOD'];

try {

    if ($method === 'GET') {
        if (isset($_GET['ma_tg'])) {
            $tg = $service->getById($_GET['ma_tg']);
            if ($tg) {
                echo json_encode($tg->toArray());
            } else {
                http_response_code(404);
                echo json_encode(["error" => "Không tìm thấy"]);
            }
        } else {
            $list = $service->getAll();
            echo json_encode(array_map(fn($tg) => $tg->toArray(), $list));
        }
    }

    
    else if ($method === 'POST') {
        $tg = new TacGiaModel(
            null,
            $_POST['ten_tg'] ?? '',
            $_POST['gioi_tinh'] ?? 0,
            $_POST['ngay_sinh'] ?? null,
            $_POST['que'] ?? '',
            $_POST['tieu_su'] ?? '',
            null
        );
        $result = $service->add($tg, $_FILES);
        echo json_encode(["success" => $result]);
    }

    
    else if ($method === 'PUT') {
        parse_str(file_get_contents("php://input"), $data);
        $tg = new TacGiaModel(
            $data['ma_tg'],
            $data['ten_tg'],
            $data['gioi_tinh'],
            $data['ngay_sinh'],
            $data['que'],
            $data['tieu_su'],
            $data['hinh'] ?? ''
        );
        echo json_encode(["success" => $service->update($tg)]);
    }

    
    else if ($method === 'DELETE') {
        $ma_tg = $_GET['ma_tg'] ?? null;
        if ($ma_tg) {
            echo json_encode(["success" => $service->delete($ma_tg)]);
        } else {
            echo json_encode(["success" => false, "message" => "Thiếu mã tác giả"]);
        }
    }
} catch (Exception $e) {
    http_response_code(400); 
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
