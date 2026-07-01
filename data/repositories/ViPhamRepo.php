<?php
class ViPhamRepo {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getThongTinPM($ma_pm) {
        $sql = "SELECT pm.*, dg.ho_ten, s.ten_sach 
                FROM phieu_muon pm
                JOIN docgia dg ON pm.ma_docgia = dg.ma_docgia
                JOIN sach s ON pm.ma_sach = s.ma_sach
                WHERE pm.ma_pm = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $ma_pm);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function addVP($vp) {
        
        $sql = "INSERT INTO vipham (ma_docgia, ly_do, ngay_vp, hinh_thuc, trang_thai, tien_phat_them, tong_tien_phat, ma_pm) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssssidds", 
            $vp->ma_docgia, 
            $vp->ly_do, 
            $vp->ngay_vp, 
            $vp->hinh_thuc, 
            $vp->trang_thai, 
            $vp->tien_phat_them, 
            $vp->tong_tien_phat, 
            $vp->ma_pm
        );
        return $stmt->execute();
    }

    public function updateVP($vp) {
        $sql = "UPDATE vipham SET ly_do=?, hinh_thuc=?, trang_thai=?, tien_phat_them=?, tong_tien_phat=? WHERE ma_vp=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sssdii", 
            $vp->ly_do, 
            $vp->hinh_thuc, 
            $vp->trang_thai, 
            $vp->tien_phat_them, 
            $vp->tong_tien_phat, 
            $vp->ma_vp
        );
        return $stmt->execute();
    }

    public function deleteVP($id) {
        $sql = "DELETE FROM vipham WHERE ma_vp = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id); 
        return $stmt->execute();
    }

    
    public function countTotal($keyword = '') {
        $sql = "SELECT COUNT(*) as total FROM vipham vp 
                JOIN docgia dg ON vp.ma_docgia = dg.ma_docgia 
                WHERE (dg.ho_ten LIKE ? 
                OR vp.ma_pm LIKE ? 
                OR vp.ma_docgia LIKE ?)";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) return 0;
        $key = "%$keyword%";
        $stmt->bind_param("sss", $key, $key, $key);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['total'];
    }

    
    public function searchVP($keyword, $offset = 0, $limit = 10) {
        $offset = (int)$offset;
        $limit = (int)$limit;
        $sql = "SELECT vp.*, dg.ho_ten 
                FROM vipham vp 
                JOIN docgia dg ON vp.ma_docgia = dg.ma_docgia 
                WHERE (dg.ho_ten LIKE ? 
                OR vp.ma_pm LIKE ? 
                OR vp.ma_docgia LIKE ?)
                ORDER BY vp.ma_vp DESC
                LIMIT $offset, $limit";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) return [];
        $key = "%$keyword%";
        $stmt->bind_param("sss", $key, $key, $key);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    public function getViPhamByPhieuMuon($ma_pm) {
        $sql = "SELECT vp.* 
                FROM vipham vp
                WHERE vp.ma_pm = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $ma_pm);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getViPhamByDocGia($ma_docgia) {
        $sql = "SELECT vp.*, pm.ten_sach, pm.ngay_muon, pm.ngay_tra_du_kien, pm.ngay_tra_thuc_te
                FROM vipham vp
                JOIN phieu_muon pm ON vp.ma_pm = pm.ma_pm
                WHERE vp.ma_docgia = ?
                ORDER BY vp.ma_vp DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $ma_docgia);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    } 

}
?>