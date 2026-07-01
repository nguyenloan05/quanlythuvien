<?php

header('Content-Type: application/json; charset=utf-8');


require_once __DIR__ . '/../services/ThuThuService.php';
require_once __DIR__ . '/../../data/models/ThuThuModel.php';

$service = new ThuThuService();
$method  = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        if (isset($_GET['ma_thuthu'])) {
            $thuthu = $service->getById($_GET['ma_thuthu']);
            if ($thuthu) {
                echo json_encode($thuthu->toArray());
            } else {
                http_response_code(404);
                echo json_encode(["error" => "Không tìm thấy thủ thư"]);
            }
        } else {
            $list = $service->listAll();
            echo json_encode(array_map(fn($tt) => $tt->toArray(), $list));
        }
    }

    else if ($method === 'POST') {
        $action = $_POST['action'] ?? '';

        if ($action === 'update') {
            if (!isset($_POST['ma_thuthu'])) {
                throw new Exception("Thiếu mã thủ thư để cập nhật");
            }

            $thuThu = new ThuThuModel(
                $_POST['ma_thuthu'], 
                $_POST['user_id'] ?? null,
                $_POST['ho_ten'] ?? '',
                !empty($_POST['ngay_sinh']) ? $_POST['ngay_sinh'] : date('Y-m-d'),
                $_POST['gioi_tinh'] ?? 'Khác',
                $_POST['email'] ?? '',
                $_POST['so_dien_thoai'] ?? '',
                $_POST['dia_chi'] ?? '',
                !empty($_POST['ngay_vao_lam']) ? $_POST['ngay_vao_lam'] : date('Y-m-d'),
                $_POST['chuc_vu'] ?? '',
                $_POST['phong_ban'] ?? '',
                $_POST['trang_thai'] ?? 'Đang làm'
            );

            $result = $service->update($thuThu);

            if ($result) {
                header("Location: ../../index.php?action=admin_dashboard&view=thuthu");
                exit();
            } else {
                echo json_encode(["success" => false, "message" => "Cập nhật thất bại"]);
            }
        } 
        else {
            $thuThu = new ThuThuModel(
                null, 
                $_POST['user_id'] ?? null,
                $_POST['ho_ten'] ?? '',
                $_POST['ngay_sinh'] ?? null,
                $_POST['gioi_tinh'] ?? 'Khác',
                $_POST['email'] ?? '',
                $_POST['so_dien_thoai'] ?? '',
                $_POST['dia_chi'] ?? '',
                $_POST['ngay_vao_lam'] ?? date('Y-m-d'),
                $_POST['chuc_vu'] ?? 'Nhân viên',
                $_POST['phong_ban'] ?? '',
                $_POST['trang_thai'] ?? 'Đang làm'
            );

            $result = $service->add($thuThu);
            echo json_encode(["success" => $result, "message" => "Thêm mới thành công"]);
        }
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false, 
        "message" => $e->getMessage(),
        "debug_post" => $_POST 
    ]);
}
?>
