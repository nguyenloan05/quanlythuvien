<?php
class PhieuMuonRepo {
    private $db;

    public function __construct($dbConnection) {
        $this->db = $dbConnection;
    }

    public function getChiTietPhieu($ma_pm) {
        $ma_pm = mysqli_real_escape_string($this->db, $ma_pm);
        $sql = "SELECT pm.*, dg.ho_ten, s.ten_sach, pm.so_luong 
                FROM phieu_muon pm
                JOIN docgia dg ON pm.ma_docgia = dg.ma_docgia
                JOIN sach s ON pm.ma_sach = s.ma_sach
                WHERE pm.ma_pm = '$ma_pm'";
        $res = mysqli_query($this->db, $sql);
        return mysqli_fetch_assoc($res);
    }

    public function getPhieuMuon($tu_khoa, $tinh_trang, $start, $limit) {
        
        $sql = "SELECT pm.*, dg.ho_ten, tt.ho_ten as ten_thu_thu, s.ten_sach 
                FROM phieu_muon pm 
                JOIN docgia dg ON pm.ma_docgia = dg.ma_docgia 
                LEFT JOIN thu_thu tt ON pm.ma_thu_thu_duyet = tt.ma_thuthu 
                LEFT JOIN sach s ON pm.ma_sach = s.ma_sach 
                WHERE 1=1"; 

        $params = [];
        $types = "";

        
        if (!empty($tu_khoa)) {
            $sql .= " AND (pm.ma_pm LIKE ? OR pm.ma_docgia LIKE ? OR dg.ho_ten LIKE ? OR s.ten_sach LIKE ?)";
            $tk = "%$tu_khoa%";
            array_push($params, $tk, $tk, $tk, $tk);
            $types .= "ssss";
        }

        
        if ($tinh_trang !== 'all' && $tinh_trang !== '') {
            $sql .= " AND pm.tinh_trang = ?";
            $params[] = (int)$tinh_trang;
            $types .= "i";
        }

        $sql .= " ORDER BY pm.ma_pm DESC LIMIT ?, ?";
        array_push($params, $start, $limit);
        $types .= "ii";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function countPhieuMuon($tu_khoa = '', $tinh_trang = 'all') {
        $sql = "SELECT COUNT(*) as total FROM phieu_muon pm 
                JOIN docgia dg ON pm.ma_docgia = dg.ma_docgia
                LEFT JOIN sach s ON pm.ma_sach = s.ma_sach
                WHERE 1=1";

        $params = [];
        $types = "";

        if (!empty($tu_khoa)) {
            $sql .= " AND (pm.ma_pm LIKE ? OR pm.ma_docgia LIKE ? OR dg.ho_ten LIKE ? OR s.ten_sach LIKE ?)";
            $search = "%$tu_khoa%";
            array_push($params, $search, $search, $search, $search);
            $types .= "ssss";
        }

        if ($tinh_trang !== 'all' && $tinh_trang !== '') {
            $sql .= " AND pm.tinh_trang = ?";
            $params[] = (int)$tinh_trang;
            $types .= "i";
        }

        $stmt = $this->db->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        return $row['total'] ?? 0;
    }

    public function checkDocGiaValid($ma_dg) {
        $ma_dg = mysqli_real_escape_string($this->db, $ma_dg);
        $ngay_hien_tai = date('Y-m-d');

        
        $sql = "SELECT trang_thai, ngay_het_han FROM the_thu_vien WHERE ma_docgia = '$ma_dg'";
        $res = mysqli_query($this->db, $sql);
        $the = mysqli_fetch_assoc($res);

        if (!$the) {
            return "Bạn chưa có thẻ thư viện. Vui lòng đăng ký thẻ để sử dụng dịch vụ mượn sách.";
        }

        
        if ($the['trang_thai'] == 0) {
            return "Thẻ thư viện đang chờ kích hoạt. Vui lòng liên hệ Admin.";
        }
        if ($the['trang_thai'] == 2) {
            return "Thẻ thư viện đã bị khóa do vi phạm hoặc yêu cầu của hệ thống.";
        }

        
        if ($the['ngay_het_han'] < $ngay_hien_tai) {
            return "Thẻ thư viện đã hết hạn sử dụng.";
        }

        
        
        $sqlOverdue = "SELECT COUNT(*) as overdue_count FROM phieu_muon 
                    WHERE ma_docgia = '$ma_dg' 
                    AND tinh_trang IN (0, 1) 
                    AND ngay_tra_du_kien < '$ngay_hien_tai'";
        $resOverdue = mysqli_query($this->db, $sqlOverdue);
        $overdue = mysqli_fetch_assoc($resOverdue);

        if ($overdue['overdue_count'] > 0) {
            return "Độc giả đang có sách quá hạn chưa trả. Không thể tạo phiếu mượn mới.";
        }

        
        $sqlPhat = "SELECT COUNT(*) as unpaid FROM vipham 
                    WHERE ma_docgia = '$ma_dg' AND trang_thai = 0";
        $resPhat = mysqli_query($this->db, $sqlPhat);
        $phat = mysqli_fetch_assoc($resPhat);

        if ($phat['unpaid'] > 0) {
            return "Độc giả có khoản phạt chưa thanh toán. Vui lòng xử lý vi phạm trước.";
        }

        return true; 
    }

    public function getSoLuongTonKho($ma_sach) {
        $ma_sach = (int)$ma_sach;
        $sql = "SELECT so_luong_hien_tai FROM sach WHERE ma_sach = $ma_sach";
        $res = mysqli_query($this->db, $sql);
        $row = mysqli_fetch_assoc($res);
        return (int)($row['so_luong_hien_tai'] ?? 0);
    }

    public function addPM($data) {
        
        $ma_docgia = mysqli_real_escape_string($this->db, $data['ma_docgia']);
        $ma_sach = (int)$data['ma_sach'];
        $so_luong_muon = (int)$data['so_luong'];
        $ngay_muon = mysqli_real_escape_string($this->db, $data['ngay_muon']);
        $ngay_tra_dk = mysqli_real_escape_string($this->db, $data['ngay_tra_dk']);
        $hinh_thuc = (int)$data['hinh_thuc'];
        $tinh_trang = (int)$data['tinh_trang'];

        
        $isValid = $this->checkDocGiaValid($ma_docgia);
        if ($isValid !== true) {
            
            return $isValid; 
        }

        
        $sqlConfig = "SELECT so_sach_toi_da FROM cai_dat_he_thong LIMIT 1";
        $resConfig = mysqli_query($this->db, $sqlConfig);
        $config = mysqli_fetch_assoc($resConfig);
        $limit = $config['so_sach_toi_da'] ?? 5;

        $dang_giu = $this->countSachDangMuon($ma_docgia);
        if (($dang_giu + $so_luong_muon) > $limit) {
            return "Vượt quá hạn mức. Bạn chỉ được mượn thêm " . ($limit - $dang_giu) . " cuốn.";
        }

        
        mysqli_begin_transaction($this->db);
        try {
            
            $sqlCheckStock = "SELECT so_luong_hien_tai FROM sach WHERE ma_sach = $ma_sach FOR UPDATE";
            $resStock = mysqli_query($this->db, $sqlCheckStock);
            $sach = mysqli_fetch_assoc($resStock);

            if (!$sach || $sach['so_luong_hien_tai'] < $so_luong_muon) {
                throw new Exception("Sách trong kho không đủ số lượng.");
            }

            
            $sqlUpdateStock = "UPDATE sach SET so_luong_hien_tai = so_luong_hien_tai - $so_luong_muon WHERE ma_sach = $ma_sach";
            if (!mysqli_query($this->db, $sqlUpdateStock)) {
                throw new Exception("Lỗi khi cập nhật kho sách.");
            }

            
            $ma_pm = $this->maPMtuDong();
            $sqlInsert = "INSERT INTO phieu_muon (ma_pm, ma_docgia, ma_sach, ngay_muon, ngay_tra_du_kien, so_luong, hinh_thuc, tinh_trang) 
                          VALUES ('$ma_pm', '$ma_docgia', $ma_sach, '$ngay_muon', '$ngay_tra_dk', $so_luong_muon, $hinh_thuc, $tinh_trang)";
            
            if (!mysqli_query($this->db, $sqlInsert)) {
                throw new Exception("Lỗi khi tạo phiếu mượn.");
            }

            
            mysqli_commit($this->db);
            return true;
        } catch (Exception $e) {
            
            mysqli_rollback($this->db);
            return $e->getMessage();
        }
    }

    public function updatePM($ma_pm, $trang_thai_moi, $ngay_tra_tt, $ngay_tra_dk, $tong_tien_phat, $ly_do_vp, $ma_thu_thu = null) {
        $trang_thai_moi = (int)$trang_thai_moi;
        $ma_tt = $ma_thu_thu ? (int)$ma_thu_thu : null;

        
        $driver = new mysqli_driver();
        $old_report_mode = $driver->report_mode;
        $driver->report_mode = MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT;

        mysqli_begin_transaction($this->db);
        try {
            
            $sqlInfo = "SELECT pm.*, s.so_luong_hien_tai 
                        FROM phieu_muon pm 
                        JOIN sach s ON pm.ma_sach = s.ma_sach 
                        WHERE pm.ma_pm = ? FOR UPDATE";
            $stmtInfo = $this->db->prepare($sqlInfo);
            $stmtInfo->bind_param("s", $ma_pm);
            $stmtInfo->execute();
            $data = $stmtInfo->get_result()->fetch_assoc();
            
            if (!$data) throw new Exception("Không tìm thấy dữ liệu phiếu mượn #$ma_pm");

            $trang_thai_cu = (int)$data['tinh_trang'];
            $so_luong = (int)$data['so_luong'];
            $ma_sach = $data['ma_sach'];
            $ma_dg = $data['ma_docgia'];

            
            $is_cu_giu = in_array($trang_thai_cu, [0, 1, 3]);
            $is_moi_giu = in_array($trang_thai_moi, [0, 1, 3]);

            if ($is_cu_giu && !$is_moi_giu) {
                
                mysqli_query($this->db, "UPDATE sach SET so_luong_hien_tai = so_luong_hien_tai + $so_luong WHERE ma_sach = $ma_sach");
            } elseif (!$is_cu_giu && $is_moi_giu) {
                
                if ($data['so_luong_hien_tai'] < $so_luong) throw new Exception("Kho không đủ sách để mượn lại.");
                mysqli_query($this->db, "UPDATE sach SET so_luong_hien_tai = so_luong_hien_tai - $so_luong WHERE ma_sach = $ma_sach");
            }

            
            $ngay_tra_val = in_array($trang_thai_moi, [2, 3]) ? $ngay_tra_tt : null;

            $sqlUpdate = "UPDATE phieu_muon SET 
                            tinh_trang = ?, 
                            ngay_tra_du_kien = ?,
                            ngay_tra_thuc_te = ?,
                            ma_thu_thu_duyet = ?
                        WHERE ma_pm = ?";
            
            $stmtUp = $this->db->prepare($sqlUpdate);
            $stmtUp->bind_param("issss", $trang_thai_moi, $ngay_tra_dk, $ngay_tra_val, $ma_tt, $ma_pm);
            $stmtUp->execute();

            
            mysqli_query($this->db, "DELETE FROM vipham WHERE ma_pm = '$ma_pm'");
            
            if ($tong_tien_phat > 0) {
                $now = date('Y-m-d');
                $hinh_thuc_vp = 'Vi phạm mượn trả';
                $trang_thai_vp = 0; 
                $phat_them_default = 0;

                $sqlPhat = "INSERT INTO vipham (ma_docgia, ly_do, ngay_vp, hinh_thuc, trang_thai, tien_phat_them, tong_tien_phat, ma_pm) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                
                $stmtPhat = $this->db->prepare($sqlPhat);
                
                $stmtPhat->bind_param("ssssidds", $ma_dg, $ly_do_vp, $now, $hinh_thuc_vp, $trang_thai_vp, $phat_them_default, $tong_tien_phat, $ma_pm);
                $stmtPhat->execute();
            }

            mysqli_commit($this->db);
            $driver->report_mode = $old_report_mode;
            return true;
        } catch (Exception $e) {
            mysqli_rollback($this->db);
            $driver->report_mode = $old_report_mode;
            return "Lỗi: " . $e->getMessage();
        }
    }

    public function deletePM($ma_pm) {
        
        $ma_pm = mysqli_real_escape_string($this->db, $ma_pm);

        
        $sql = "SELECT tinh_trang FROM phieu_muon WHERE ma_pm = '$ma_pm'";
        $res = mysqli_query($this->db, $sql);
        $row = mysqli_fetch_assoc($res);

        if (!$row) {
            return 'not_found'; 
        }

        $trang_thai = (int)$row['tinh_trang'];

        
        if (!in_array($trang_thai, [2, 4])) {
            return 'error_status_denied'; 
        }

        
        mysqli_begin_transaction($this->db);
        try {
            
            mysqli_query($this->db, "DELETE FROM vipham WHERE ma_pm = '$ma_pm'");

            
            mysqli_query($this->db, "DELETE FROM phieu_muon WHERE ma_pm = '$ma_pm'");

            mysqli_commit($this->db);
            return 'success';
        } catch (Exception $e) {
            mysqli_rollback($this->db);
            return 'error_system'; 
        }
    }

    public function maPMtuDong() {
        $check = true; 
        $maPM = "";
        while ($check) {
            $maPM = "PM" . date('ymd') . strtoupper(substr(md5(uniqid()), 0, 2));
            $sql = "SELECT ma_pm FROM phieu_muon WHERE ma_pm = '$maPM'";
            $res = mysqli_query($this->db, $sql);
            if (mysqli_num_rows($res) == 0) $check = false;
        }
        return $maPM;
    }

    public function countSachDangMuon($ma_docgia) {
        $ma_docgia = mysqli_real_escape_string($this->db, $ma_docgia);
        $sql = "SELECT SUM(so_luong) as total_dang_giu 
                FROM phieu_muon 
                WHERE ma_docgia = '$ma_docgia' AND tinh_trang IN (0, 1)";
        $res = mysqli_query($this->db, $sql);
        $row = mysqli_fetch_assoc($res);
        return (int)($row['total_dang_giu'] ?? 0);
    }

    public function getAllDocGia() {
        $sql = "SELECT dg.ma_docgia, dg.ho_ten 
                FROM docgia dg
                INNER JOIN the_thu_vien ttv ON dg.ma_docgia = ttv.ma_docgia
                WHERE ttv.trang_thai = 1
                AND ttv.ngay_het_han >= CURDATE()";
                
        return mysqli_query($this->db, $sql);
    }

    public function getAllSachAvailable() {
        
        $sql = "SELECT s.ma_sach, s.ten_sach, s.so_luong_hien_tai, s.vi_tri, tg.ten_tg as tac_gia 
                FROM sach s
                LEFT JOIN tac_gia tg ON s.ma_tg = tg.ma_tg
                WHERE s.so_luong_hien_tai > 0 
                AND s.tinh_trang = 1";
        return mysqli_query($this->db, $sql);
    }

    public function ds_phantrang($limit, $offset) {
        $sql = "SELECT pm.*, s.ten_sach, dg.ho_ten, tt.ho_ten as ten_thu_thu 
                FROM phieu_muon pm
                LEFT JOIN sach s ON pm.ma_sach = s.ma_sach
                LEFT JOIN docgia dg ON pm.ma_docgia = dg.ma_docgia
                LEFT JOIN thu_thu tt ON pm.ma_thu_thu_duyet = tt.ma_thuthu
                ORDER BY pm.ngay_muon DESC
                LIMIT ? OFFSET ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $list = [];
        while ($row = $result->fetch_assoc()) {
            $list[] = $row;
        }
        return $list;
    }

    public function tong_so_phieu() {
        $sql = "SELECT COUNT(*) as total FROM phieu_muon";
        $result = $this->db->query($sql);
        $row = $result->fetch_assoc();
        return (int)$row['total'];
    }
}