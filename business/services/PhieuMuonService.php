<?php
class PhieuMuonService {
    private $repo;

    public function __construct($db) {
        $this->repo = new PhieuMuonRepo($db);
    }

    
    public function getList($params) {
        $tu_khoa = $params['tu_khoa'] ?? '';
        $tinh_trang = $params['tinh_trang'] ?? 'all';
        $page = isset($params['page']) ? (int)$params['page'] : 1;
        $limit = isset($params['limit']) ? (int)$params['limit'] : 10;
        $start = ($page - 1) * $limit;

        $data = $this->repo->getPhieuMuon($tu_khoa, $tinh_trang, $start, $limit);
        $total = $this->repo->countPhieuMuon($tu_khoa, $tinh_trang);

        $list = [];
        if ($data instanceof mysqli_result) {
            while ($row = $data->fetch_assoc()) {
                $list[] = $row;
            }
        }

        return [
            'status' => 'success',
            'data' => $list,
            'pagination' => [
                'current_page' => $page,
                'limit' => $limit,
                'total_records' => (int)$total,
                'total_pages' => ($limit > 0) ? ceil($total / $limit) : 1
            ]
        ];
    }

    public function create($data) {
        if (empty($data['ma_docgia']) || empty($data['ma_sach'])) {
            return ['status' => 'error', 'message' => 'Thông tin không được để trống.'];
        }

        
        
        $result = $this->repo->addPM($data);

        if ($result === true) {
            return ['status' => 'success', 'message' => 'Thêm phiếu mượn thành công.'];
        } else {
            
            return [
                'status' => 'error', 
                'message' => $result 
            ];
        }
    }

    public function updateTraSach($ma_pm, $new_status, $ma_thu_thu, $data_update) {
        try {
            
            $ngay_tra_tt      = $data_update['ngay_tra_tt'] ?? date('Y-m-d');
            $ngay_tra_dk      = $data_update['ngay_tra_dk'] ?? null;
            $tong_tien_phat   = (float)($data_update['tong_tien_vp'] ?? 0);
            $ly_do_vp         = $data_update['ly_do_vp'] ?? '';

            $result = $this->repo->updatePM(
                $ma_pm, 
                $new_status, 
                $ngay_tra_tt, 
                $ngay_tra_dk, 
                $tong_tien_phat, 
                $ly_do_vp, 
                $ma_thu_thu
            );

            if ($result === true) {
                return ['status' => 'success', 'message' => 'Cập nhật thành công'];
            } else {
                return ['status' => 'error', 'message' => $result];
            }
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function delete($ma_pm) {
        $res = $this->repo->deletePM($ma_pm);

        
        switch ($res) {
            case 'success':
                return ['status' => 'success', 'message' => 'Xóa phiếu mượn thành công!'];

            case 'error_status_denied':
                return ['status' => 'error', 'message' => 'Không thể xóa phiếu đang ở trạng thái Chờ duyệt, Đang mượn hoặc Vi phạm.'];

            case 'not_found':
                return ['status' => 'error', 'message' => 'Không tìm thấy phiếu mượn này.'];

            case 'error_system':
                return ['status' => 'error', 'message' => 'Lỗi hệ thống khi xóa phiếu.'];

            default:
                return ['status' => 'error', 'message' => 'Lỗi không xác định.'];
        }
    }

    public function getDetail($ma_pm) {
        $detail = $this->repo->getChiTietPhieu($ma_pm); 
        if ($detail) {
            return ['status' => 'success', 'data' => $detail];
        }
        return ['status' => 'error', 'message' => 'Không tìm thấy thông tin phiếu mượn.'];
    }

    public function formData() {
        
        $list_docgia = $this->repo->getAllDocGia(); 
        $list_sach = $this->repo->getAllSachAvailable();

        return [
            'list_docgia' => $list_docgia,
            'list_sach' => $list_sach
        ];
    }
}