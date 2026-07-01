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
            $_POST['dia_chi'] ?? '',
            $_POST['ghi_chu_vi_pham'] ?? null
        );

        // --- 1. LUỒNG CẬP NHẬT ---
        if ($action === 'update_profile' || isset($_POST['is_update'])) {
            $result = $service->update($docgia, $_FILES);

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

        // --- 1.1 LUỒNG ĐỔI MẬT KHẨU ---
        if ($action === 'change_password') {
            $user_id = $_POST['user_id'] ?? null;
            $current_password = $_POST['current_password'] ?? '';
            $new_password = $_POST['new_password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';

            if (empty($user_id) || empty($current_password) || empty($new_password) || empty($confirm_password)) {
                header("Location: ../../index.php?action=trangchu_sv&view=doimkDocGia&error=" . urlencode("Vui lòng nhập đầy đủ các trường"));
                exit;
            }

            // Đối chiếu mật khẩu cũ
            $db_password = $service->getPassword($user_id);
            if ($db_password === null || $current_password !== $db_password) {
                header("Location: ../../index.php?action=trangchu_sv&view=doimkDocGia&error=" . urlencode("Mật khẩu hiện tại không chính xác"));
                exit;
            }

            if ($new_password !== $confirm_password) {
                header("Location: ../../index.php?action=trangchu_sv&view=doimkDocGia&error=" . urlencode("Xác nhận mật khẩu mới không trùng khớp"));
                exit;
            }

            // Định dạng mật khẩu: từ 6-20 ký tự, bao gồm cả chữ và số
            if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{6,20}$/', $new_password)) {
                header("Location: ../../index.php?action=trangchu_sv&view=doimkDocGia&error=" . urlencode("Mật khẩu mới phải từ 6-20 ký tự, bao gồm cả chữ và số "));
                exit;
            }

            if ($current_password === $new_password) {
                header("Location: ../../index.php?action=trangchu_sv&view=doimkDocGia&error=" . urlencode("Mật khẩu mới không được giống mật khẩu hiện tại"));
                exit;
            }

            $service->updatePassword($user_id, $new_password);
            header("Location: ../../index.php?action=trangchu_sv&view=doimkDocGia&success=" . urlencode("Thay đổi mật khẩu thành công"));
            exit;
        }

        // --- 2. LUỒNG THÊM MỚI ---
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $ma_dg = $_POST['ma_docgia'] ?? '';
        $email = $_POST['email'] ?? '';

        if (empty($username) || empty($password) || empty($ma_dg)) {
            header("Location: ../../index.php?action=admin_dashboard&view=addDocGia&error=" . urlencode("Vui lòng điền đầy đủ thông tin bắt buộc"));
            exit;
        }
        if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{6,20}$/', $password)) {
            header("Location: ../../index.php?action=admin_dashboard&view=addDocGia&error=" . urlencode("Mật khẩu phải từ 6-20 ký tự, bao gồm cả chữ cái và chữ số"));
            exit;
        }
        // --- BỔ SUNG KIỂM TRA ĐỊNH DẠNG EMAIL ---
        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: ../../index.php?action=admin_dashboard&view=addDocGia&error=" . urlencode("Email không hợp lệ (VD: abc@gmail.com)"));
    exit;
}

        // Kiểm tra trùng lặp tập trung
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
            // Chọn ngẫu nhiên 1 trong các ảnh dd1.jfif -> dd5.jfif làm ảnh mặc định
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