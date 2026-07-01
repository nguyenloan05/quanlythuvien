<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../../services/docgia/pd_DocGiaService.php';

$service = new pd_DocGiaService();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $keyword = $_GET['search'] ?? '';
        $user_id = $_SESSION['user_id'] ?? 10;
        $result = $service->getList($keyword, $user_id);
        echo json_encode($result);
        break;

    case 'POST':
        $action = $_POST['action'] ?? '';
        $user_id = $_SESSION['user_id'] ?? 10;

        switch ($action) {
            case 'datSach':
                $ma_sach = $_POST['ma_sach'] ?? null;
                if (!$ma_sach) {
                    echo json_encode(["success" => false, "message" => "Thiếu mã sách"]);
                    break;
                }

                $ma_docgia = $service->getMaDocGia($user_id);

                if (!$ma_docgia) {
                    echo json_encode(["success" => false, "message" => "Không tìm thấy mã độc giả"]);
                    break;
                }

                $res = $service->datSach($ma_docgia, $ma_sach);
                echo json_encode($res);
                break;

            case 'huyDat':
                $ma_pdt = $_POST['ma_pdt'] ?? null;
                $ma_sach = $_POST['ma_sach'] ?? null;

                if (!$ma_sach) {
                    echo json_encode(["success" => false, "message" => "Thiếu dữ liệu hủy"]);
                    break;
                }

                $ma_docgia = $service->getMaDocGia($user_id);

                $res = $service->huyDat($ma_pdt, $ma_docgia, $ma_sach);
                echo json_encode($res);
                break;

            default:
                echo json_encode(["success" => false, "message" => "Action không hợp lệ"]);
                break;
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["success" => false, "message" => "Method not allowed"]);
        break;
}
