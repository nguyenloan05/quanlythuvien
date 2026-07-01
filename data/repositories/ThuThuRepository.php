<?php
require_once __DIR__ . '/../models/ThuThuModel.php';

class ThuThuRepository {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    
    public function getAll() {
        $list = [];
        $sql = "SELECT * FROM thu_thu";
        $result = $this->conn->query($sql);
        
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $list[] = $this->mapToModel($row);
            }
        }
        return $list;
    }

    
    public function getById($ma_thuthu) {
        $sql = "SELECT * FROM thu_thu WHERE ma_thuthu = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $ma_thuthu);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            return $this->mapToModel($row);
        }
        return null;
    }

    
    public function search($keyword) {
        $list = [];
        $kw = "%" . $keyword . "%";
        $sql = "SELECT * FROM thu_thu WHERE ma_thuthu LIKE ? OR ho_ten LIKE ? OR so_dien_thoai LIKE ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sss", $kw, $kw, $kw);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $list[] = $this->mapToModel($row);
        }
        return $list;
    }

    
    public function add(ThuThuModel $thuthu) {
        $sql = "INSERT INTO thu_thu (ma_thuthu, user_id, ho_ten, ngay_sinh, gioi_tinh, email, so_dien_thoai, dia_chi, ngay_vao_lam, chuc_vu, phong_ban, trang_thai) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        
        $ma = $thuthu->getMaThuThu();
        $user_id = $thuthu->getUserId();
        $ho_ten = $thuthu->getHoTen();
        $ngay_sinh = $thuthu->getNgaySinh();
        $gioi_tinh = $thuthu->getGioiTinh();
        $email = $thuthu->getEmail();
        $sdt = $thuthu->getSoDienThoai();
        $dia_chi = $thuthu->getDiaChi();
        $ngay_vao_lam = $thuthu->getNgayVaoLam();
        $chuc_vu = $thuthu->getChucVu();
        $phong_ban = $thuthu->getPhongBan();
        $trang_thai = $thuthu->getTrangThai();

        $stmt->bind_param("sisssssssssi", $ma, $user_id, $ho_ten, $ngay_sinh, $gioi_tinh, $email, $sdt, $dia_chi, $ngay_vao_lam, $chuc_vu, $phong_ban, $trang_thai);
        return $stmt->execute();
    }

    
    public function update(ThuThuModel $thuthu) {
        $sql = "UPDATE thu_thu SET ho_ten=?, ngay_sinh=?, gioi_tinh=?, email=?, so_dien_thoai=?, dia_chi=?, ngay_vao_lam=?, chuc_vu=?, phong_ban=?, trang_thai=? 
                WHERE ma_thuthu=?";
        $stmt = $this->conn->prepare($sql);

        $ho_ten = $thuthu->getHoTen();
        $ngay_sinh = $thuthu->getNgaySinh();
        $gioi_tinh = $thuthu->getGioiTinh();
        $email = $thuthu->getEmail();
        $sdt = $thuthu->getSoDienThoai();
        $dia_chi = $thuthu->getDiaChi();
        $ngay_vao_lam = $thuthu->getNgayVaoLam();
        $chuc_vu = $thuthu->getChucVu();
        $phong_ban = $thuthu->getPhongBan();
        $trang_thai = $thuthu->getTrangThai();
        $ma = $thuthu->getMaThuThu();

        $stmt->bind_param("sssssssssis", $ho_ten, $ngay_sinh, $gioi_tinh, $email, $sdt, $dia_chi, $ngay_vao_lam, $chuc_vu, $phong_ban, $trang_thai, $ma);
        return $stmt->execute();
    }

    
    public function delete($ma_thuthu) {
        $sql = "DELETE FROM thu_thu WHERE ma_thuthu = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $ma_thuthu);
        return $stmt->execute();
    }

    
    private function mapToModel($row) {
        return new ThuThuModel(
            $row['ma_thuthu'], $row['user_id'], $row['ho_ten'], $row['ngay_sinh'], 
            $row['gioi_tinh'], $row['email'], $row['so_dien_thoai'], $row['dia_chi'], 
            $row['ngay_vao_lam'], $row['chuc_vu'], $row['phong_ban'], $row['trang_thai']
        );
    }
}
