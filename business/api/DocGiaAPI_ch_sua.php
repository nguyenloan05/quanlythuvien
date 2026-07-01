<?php

header('Content-Type: application/json; charset=utf-8');


require_once __DIR__ . '/../services/DocGiaService.php';
require_once __DIR__ . '/../../data/models/DocGiaModel.php';

$service = new DocGiaService();
$method  = $_SERVER['REQUEST_METHOD'];

try {
    
    if ($method === 'GET') {
        
        
        if (isset($_GET['ma_docgia'])) {
            AuthMiddleware::checkLogin();
            $docgia = $service->getById($_GET['ma_docgia']);

            if ($docgia) {
                echo json_encode($docgia->toArray(), JSON_UNESCAPED_UNICODE);
            } else {
                http_response_code(404);
                echo json_encode(["error" => "Không tìm thấy thông tin độc giả"], JSON_UNESCAPED_UNICODE);
            }
        }
        
        
        else if (isset($_GET['keyword'])) {
            $list = $service->search($_GET['keyword']);
            echo json_encode(array_map(fn($dg) => $dg->toArray(), $list), JSON_UNESCAPED_UNICODE);
        }
        
        
        else {
            $list = $service->getAll();
            echo json_encode(array_map(fn($dg) => $dg->toArray(), $list), JSON_UNESCAPED_UNICODE);
        }
    }

    
    else if ($method === 'POST') {
        $action = $_POST['action'] ?? '';
        
        $docgia = new DocGiaModel(
            $_POST['ma_docgia'] ?? null, 
            $_POST['user_id'] ?? null,
            $_POST['ho_ten'] ?? '',
            $_POST['ngay_sinh'] ?? null,
            $_POST['gioi_tinh'] ?? '',
            $_POST['email'] ?? '',
            $_POST['so_dien_thoai'] ?? '',
            $_POST['dia_chi'] ?? '',
            null, 
            $_POST['ghi_chu_vi_pham'] ?? null
        );

        // --- 1. LUỒNG CẬP NHẬT ---
        if ($action === 'update_profile' || isset($_POST['is_update'])) {
            $result = $service->update($docgia, $_FILES);
            
            if (!empty($_POST['new_password']) && !empty($_POST['user_id'])) {
                $service->updatePassword($_POST['user_id'], $_POST['new_password']);
            }

            if (isset($_POST['redirect'])) {
                header("Location: ../../index.php?action=admin_dashboard&view=docgia");
                exit;
            } else if ($action === 'update_profile') {
                header("Location: ../../index.php?action=trangchu_sv&view=taikhoan");
                exit;
            }
            
            echo json_encode(["success" => $result], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // --- 2. LUỒNG THÊM MỚI ---
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $ma_dg = $_POST['ma_docgia'] ?? '';

        if (empty($username) || empty($password) || empty($ma_dg)) {
            header("Location: ../../index.php?action=admin_dashboard&view=addDocGia&error=" . urlencode("Vui lòng điền đầy đủ thông tin bắt buộc"));
            exit;
        }

        $dupes = $service->checkTrung($username, $ma_dg);
        if ($dupes['u_exists'] > 0) {
            header("Location: ../../index.php?action=admin_dashboard&view=addDocGia&error=" . urlencode("Tên đăng nhập đã tồn tại"));
            exit;
        }
        if ($dupes['dg_exists'] > 0) {
            header("Location: ../../index.php?action=admin_dashboard&view=addDocGia&error=" . urlencode("Mã độc giả đã tồn tại"));
            exit;
        }

        try {
            $random_img = 'dd' . rand(1, 5) . '.jfif';
            $docgia->setAnhChanDung($random_img);
            if ($service->createWithAccount($docgia, $username, $password)) {
                header("Location: ../../index.php?action=admin_dashboard&view=docgia");
                exit;
            }
        } catch (Exception $e) {
            header("Location: ../../index.php?action=admin_dashboard&view=addDocGia&error=" . urlencode($e->getMessage()));
            exit;
        }
    }

    
    else if ($method === 'PUT') {
        
        
        parse_str(file_get_contents("php://input"), $data);

        $docgia = new DocGiaModel(
            $data['ma_docgia'] ?? null,
            $data['user_id'] ?? null,
            $data['ho_ten'] ?? '',
            $data['ngay_sinh'] ?? null,
            $data['gioi_tinh'] ?? '',
            $data['email'] ?? '',
            $data['so_dien_thoai'] ?? '',
            $data['dia_chi'] ?? '',
            $data['anh_chan_dung'] ?? '', 
            $data['ghi_chu_vi_pham'] ?? null
        );

        $result = $service->update($docgia);
        echo json_encode(["success" => $result], JSON_UNESCAPED_UNICODE);
    }

    
    else if ($method === 'DELETE') {
        
        parse_str(file_get_contents("php://input"), $data);
        
        if (isset($data['ma_docgia'])) {
            $result = $service->delete($data['ma_docgia']);
            echo json_encode(["success" => $result], JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Thiếu mã độc giả cần xóa"], JSON_UNESCAPED_UNICODE);
        }
    }

    
    else {
        http_response_code(405);
        echo json_encode(["error" => "Method không hợp lệ"], JSON_UNESCAPED_UNICODE);
    }

} catch (Exception $e) {
    
    http_response_code(500);
    echo json_encode(["error" => "Lỗi Server: " . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}