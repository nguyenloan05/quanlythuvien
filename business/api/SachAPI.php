<?php
require_once __DIR__ . '/../services/SachService.php';
require_once __DIR__ . '/../../data/models/SachModel.php';

header('Content-Type: application/json');

$service = new SachService();
$method  = $_SERVER['REQUEST_METHOD'];

try {
    
    if ($method === 'GET') {
        if (isset($_GET['ma_sach'])) {
            $sach = $service->getById($_GET['ma_sach']);
            if ($sach) {
                echo json_encode($sach->toArray());
            } else {
                http_response_code(404);
                echo json_encode(["error" => "Không tìm thấy"]);
            }
        } else if (isset($_GET['keyword'])) {
            $list = $service->search($_GET['keyword']);
            echo json_encode(array_map(fn($s) => $s->toArray(), $list));
        } else {
            $list = $service->getAll();
            echo json_encode(array_map(fn($s) => $s->toArray(), $list));
        }
    }

    
    else if ($method === 'POST') {
        $sach = new SachModel(
            null,
            $_POST['ten_sach'] ?? '',
            $_POST['ma_loai_sach'] ?? '',
            $_POST['nha_xb'] ?? '',
            $_POST['nam_xb'] ?? null,
            $_POST['tinh_trang'] ?? 1,
            $_POST['mo_ta'] ?? '',
            null,
            $_POST['ma_tg'] ?? null,
            $_POST['so_luong_tong'] ?? 0,
            $_POST['so_luong_hien_tai'] ?? null,
            $_POST['vi_tri'] ?? ''
        );
        $result = $service->add($sach, $_FILES);
        echo json_encode(["success" => $result]);
    }

    
    else if ($method === 'PUT') {
        parse_str(file_get_contents("php://input"), $data);
        $sach = new SachModel(
            $data['ma_sach'],
            $data['ten_sach'],
            $data['ma_loai_sach'],
            $data['nha_xb'],
            $data['nam_xb'],
            $data['tinh_trang'],
            $data['mo_ta'],
            $data['image'] ?? '',
            $data['ma_tg'],
            $data['so_luong_tong'],
            $data['so_luong_hien_tai'],
            $data['vi_tri']
        );
        echo json_encode(["success" => $service->update($sach)]);
    }

    
    else if ($method === 'DELETE') {
        $ma_sach = $_GET['ma_sach'] ?? null;
        if ($ma_sach) {
            echo json_encode(["success" => $service->delete($ma_sach)]);
        } else {
            echo json_encode(["success" => false, "message" => "Thiếu mã sách"]);
        }
    } else {
        http_response_code(405);
        echo json_encode(["error" => "Method không hợp lệ"]);
    }
} catch (Exception $e) {
    http_response_code(400); 
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
