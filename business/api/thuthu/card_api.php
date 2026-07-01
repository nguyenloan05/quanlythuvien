<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../services/thuthu/CardService.php';

$service = new CardService();
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $id = $_GET['id'] ?? null;

        if ($id) {

            $result = $service->getById($id);

            if ($result) {

                echo json_encode([
                    "success" => true,
                    "data" => $result
                ]);
            } else {

                http_response_code(404);

                echo json_encode([
                    "success" => false,
                    "message" => "Không tìm thấy thẻ"
                ]);
            }
        } else {

            $result = $service->getAll();

            echo json_encode([
                "success" => true,
                "data" => $result
            ]);
        }

        break;

    case 'POST':
        // Xử lý các action khác nhau qua POST
        $action = $_POST['action'] ?? '';

        switch ($action) {
            case 'create':
                $ma_docgia = $_POST['ma_docgia'] ?? null;
                if (!$ma_docgia) {
                    http_response_code(400);
                    echo json_encode([
                        "success" => false,
                        "message" => "Thiếu mã độc giả"
                    ]);
                    break;
                }
                $result = $service->requestNewCard($ma_docgia);
                echo json_encode([
                    "success" => $result !== false,
                    "message" => $result !== false ? "Đăng ký thẻ thành công" : "Đăng ký thẻ thất bại"
                ]);
                break;

            case 'activate_auto':
                $id = $_POST['id'] ?? null;
                if (!$id) {
                    http_response_code(400);
                    echo json_encode([
                        "success" => false,
                        "message" => "Thiếu ID"
                    ]);
                    break;
                }
                $result = $service->activateAuto($id);
                echo json_encode([
                    "success" => $result !== false,
                    "message" => $result !== false ? "Duyệt thẻ tự động thành công" : "Duyệt thẻ tự động thất bại"
                ]);
                break;

            case 'lock':
                $id = $_POST['id'] ?? null;
                $ly_do = $_POST['ly_do_khoa'] ?? '';
                if (!$id) {
                    http_response_code(400);
                    echo json_encode([
                        "success" => false,
                        "message" => "Thiếu ID"
                    ]);
                    break;
                }
                $result = $service->lock($id, $ly_do);
                echo json_encode([
                    "success" => $result !== false,
                    "message" => $result !== false ? "Khóa thẻ thành công" : "Khóa thẻ thất bại"
                ]);
                break;

            case 'unlock':
                $id = $_POST['id'] ?? null;
                if (!$id) {
                    http_response_code(400);
                    echo json_encode([
                        "success" => false,
                        "message" => "Thiếu ID"
                    ]);
                    break;
                }
                $result = $service->unlock($id);
                echo json_encode([
                    "success" => $result !== false,
                    "message" => $result !== false ? "Mở khóa thẻ thành công" : "Mở khóa thẻ thất bại"
                ]);
                break;

            default:
                http_response_code(400);
                echo json_encode([
                    "success" => false,
                    "message" =>
                    "Action không hợp lệ"
                ]);
                break;
        }
        break;

    default:
        http_response_code(405);
        echo json_encode([
            "success" => false,
            "message" =>
            "Method not allowed"
        ]);
        break;
}
