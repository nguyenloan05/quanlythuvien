<?php

require_once __DIR__ . '/../../data/connect.php';
require_once __DIR__ . '/../../data/models/ViPhamModel.php';
require_once __DIR__ . '/../../data/repositories/ViPhamRepo.php';
require_once __DIR__ . '/../services/ViPhamService.php';

$db = (new Database())->getConnection();
$repo = new ViPhamRepo($db);
$action = $_GET['action'] ?? '';

switch($action) {
    case 'create': 
        $vp = new ViPhamModel($_POST);
        if ($repo->addVP($vp)) {
            
            $sql_update = "UPDATE phieu_muon SET tinh_trang = 3 WHERE ma_pm = ?";
            $stmt = $db->prepare($sql_update);
            $stmt->bind_param("s", $vp->ma_pm);
            $stmt->execute();
            
            header("Location: ../../presentation/views/ThuThuView/index_tt.php?view=qlvp&status=success");
            exit();
        } else {
            header("Location: ../../presentation/views/ThuThuView/index_tt.php?view=qlvp&status=error");
            exit();
        }
        break;

    case 'update':
        $id = $_GET['id'] ?? $_POST['ma_vp'] ?? ''; 
        
        if (empty($id)) {
            header("Location: ../../presentation/views/ThuThuView/index_tt.php?view=qlvp&status=error");
            exit();
        }

        
        $ly_do = $_POST['ly_do'] ?? '';
        $tien_phat_them = $_POST['tien_phat_them'] ?? 0;
        $tong_tien_phat = $_POST['tong_tien_phat'] ?? 0;
        $trang_thai = $_POST['trang_thai'] ?? 0;

        
        $sql_update_vp = "UPDATE vipham SET ly_do = ?, tien_phat_them = ?, tong_tien_phat = ?, trang_thai = ? WHERE ma_vp = ?";
        $stmt_vp = $db->prepare($sql_update_vp);
        $stmt_vp->bind_param("sddii", $ly_do, $tien_phat_them, $tong_tien_phat, $trang_thai, $id);

        if ($stmt_vp->execute()) {
            
            if ($trang_thai == 1) {
                $sql_get_pm = "SELECT ma_pm FROM vipham WHERE ma_vp = ?";
                $stmt_get = $db->prepare($sql_get_pm);
                $stmt_get->bind_param("i", $id);
                $stmt_get->execute();
                $res = $stmt_get->get_result()->fetch_assoc();

                if ($res) {
                    $sql_update_pm = "UPDATE phieu_muon SET tinh_trang = 2 WHERE ma_pm = ?";
                    $stmt_pm = $db->prepare($sql_update_pm);
                    $stmt_pm->bind_param("s", $res['ma_pm']);
                    $stmt_pm->execute();
                }
            }

            header("Location: ../../presentation/views/ThuThuView/index_tt.php?view=qlvp&status=updated");
            exit();
        } else {
            header("Location: ../../presentation/views/ThuThuView/index_tt.php?view=qlvp&status=error");
            exit();
        }
        break;
    case 'delete':
        
        $id = $_GET['id'] ?? '';
    
        
        $sql_check = "SELECT trang_thai FROM vipham WHERE ma_vp = ?";
        $stmt_check = $db->prepare($sql_check);
        $stmt_check->bind_param("i", $id);
        $stmt_check->execute();
        $result = $stmt_check->get_result()->fetch_assoc();

        
        if ($result && $result['trang_thai'] == '1') {
            if ($repo->deleteVP($id)) {
                header("Location: ../../presentation/views/ThuThuView/index_tt.php?view=qlvp&status=deleted");
                exit();
            } else {
                header("Location: ../../presentation/views/ThuThuView/index_tt.php?view=qlvp&status=error");
                exit();
            }
        } else {
            
            echo "<script>
                    alert('Không thể xóa! Chỉ biên bản đã hoàn thành thanh toán tiền vi phạm mới được phép xóa.');
                    window.location.href = '../../presentation/views/ThuThuView/index_tt.php?view=qlvp';
                </script>";
            exit();
        }
        break;
}