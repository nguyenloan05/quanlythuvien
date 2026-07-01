<?php
header("Content-Type: application/json; charset=UTF-8");
require_once __DIR__ . '/../../data/connect.php';
require_once __DIR__ . '/../../data/repositories/PhieuMuonRepo.php';
require_once __DIR__ . '/../services/PhieuMuonService.php';

$db = new Database();
$conn = $db->getConnection();

$service = new PhieuMuonService($conn);
$action = $_GET['action'] ?? '';


$ma_thu_thu_session = 1; 

switch ($action) {
    case 'get_sach_available':
        $result = $service->formData(); 
        $list_sach = [];

        if ($result['list_sach'] instanceof mysqli_result) {
            while ($row = mysqli_fetch_assoc($result['list_sach'])) {
                $list_sach[] = $row;
            }
        }
        echo json_encode($list_sach);
        break;

    case 'list':
        $params = [
            'tu_khoa' => $_GET['search'] ?? '',
            'tinh_trang' => $_GET['status'] ?? 'all',
            'page' => $_GET['page'] ?? 1
        ];
        echo json_encode($service->getList($params));
        break;

    case 'add':
        $data = [
            'ma_docgia' => $_POST['ma_docgia'] ?? '',
            'ma_sach' => $_POST['ma_sach'] ?? 0,
            'ngay_muon' => date('Y-m-d'),
            'ngay_tra_dk' => $_POST['ngay_tra_dk'] ?? '',
            'so_luong' => $_POST['so_luong'] ?? 1,
            'hinh_thuc' => $_POST['hinh_thuc'] ?? 1, 
            'tinh_trang' => $_POST['tinh_trang'] ?? 0
        ];
        echo json_encode($service->create($data));
        break;

    case 'update_status':

        $ma_pm = $_POST['ma_pm'] ?? '';
        $new_status = (int)$_POST['tinh_trang'] ?? 0; 
        $tien_muon = (float)($_POST['tien_phat_muon'] ?? 0);
        $tien_hong = (float)($_POST['tien_phat_them'] ?? 0);
        $tong_phat = $tien_muon + $tien_hong;

        $data_update = [
            'ngay_tra_dk'   => $_POST['ngay_tra_dk'] ?? null,
            'ngay_tra_tt'   => $_POST['ngay_tra_tt'] ?? date('Y-m-d'),
        
            'ly_do_vp'      => $_POST['ly_do_vp'] ?? '',
            'tong_tien_vp'  => $tong_phat
        ];


        $result = $service->updateTraSach($ma_pm, $new_status, $ma_thu_thu_session, $data_update);

        echo json_encode($result);
        exit();
        break;

    case 'delete':
        if (ob_get_length()) ob_clean(); 

        $ma_pm = $_POST['ma_pm'] ?? '';
        if (empty($ma_pm)) {
            echo json_encode(['status' => 'error', 'message' => 'Không nhận được mã phiếu']);
            exit;
        }

        $res = $service->delete($ma_pm);
        echo json_encode($res);
        exit;

    case 'get_stats':
        $repo = new PhieuMuonRepo($conn);
        $search = $_GET['search'] ?? '';
        echo json_encode([
            'status' => 'success',
            'data' => [
                'all' => $repo->countPhieuMuon($search, 'all'),
                'pending' => $repo->countPhieuMuon($search, '0'),
                'borrowing' => $repo->countPhieuMuon($search, '1'),
                'returned' => $repo->countPhieuMuon($search, '2'),
                'violation' => $repo->countPhieuMuon($search, '3'), 
                'cancelled' => $repo->countPhieuMuon($search, '4')  
            ]
        ]);
        break;

    case 'detail':
        $ma_pm = $_GET['ma_pm'] ?? '';
        echo json_encode($service->getDetail($ma_pm));
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Hành động không hợp lệ.']);
        break;
}
?>