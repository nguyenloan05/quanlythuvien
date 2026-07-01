<?php
require_once __DIR__ . '/../services/TheLoaiService.php';
require_once __DIR__ . '/../../data/models/TheLoaiModel.php';

header('Content-Type: application/json');

$service = new TheLoaiService();
$method  = $_SERVER['REQUEST_METHOD'];

try {
    
    if ($method === 'GET') {
        if (isset($_GET['ma_loai_sach'])) {
            $ls = $service->getById($_GET['ma_loai_sach']);
            if ($ls) {
                echo json_encode($ls->toArray());
            } else {
                http_response_code(404);
                echo json_encode(["error" => "Không tìm thấy"]);
            }
        } else {
            $list = $service->getAll();
            echo json_encode(array_map(fn($ls) => $ls->toArray(), $list));
        }
    }

    
    else if ($method === 'POST') {
        $ls = new TheLoaiModel(
            $_POST['ma_loai_sach'] ?? '',
            $_POST['ten_loai_sach'] ?? ''
        );
        $result = $service->add($ls);
        echo json_encode(["success" => $result]);
    }

    
    else if ($method === 'PUT') {
        parse_str(file_get_contents("php://input"), $data);
        $ls = new TheLoaiModel(
            $data['ma_loai_sach'],
            $data['ten_loai_sach']
        );
        echo json_encode(["success" => $service->update($ls)]);
    }
    
    
    else if ($method === 'DELETE') {
        
        $ma_loai = $_GET['ma_loai_sach'] ?? null;
        if ($ma_loai) {
            $result = $service->delete($ma_loai);
            echo json_encode(["success" => $result]);
        } else {
            echo json_encode(["success" => false, "message" => "Thiếu mã loại"]);
        }
    }
} catch (Exception $e) {
    http_response_code(400); 
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
