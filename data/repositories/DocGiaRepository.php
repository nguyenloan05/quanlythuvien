<?php
require_once __DIR__ . '/../models/DocGiaModel.php';

class DocGiaRepository {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    
    public function getAll() {
        $list = [];
        $sql = "SELECT * FROM docgia";
        $result = $this->conn->query($sql);
        
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $list[] = $this->mapToModel($row);
            }
        }
        return $list;
    }

    
    public function getById($ma_docgia) {//Tìm chính xác một độc giả dựa trên mã định danh
        $sql = "SELECT * FROM docgia WHERE ma_docgia = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $ma_docgia);
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
        $sql = "SELECT * FROM docgia WHERE ma_docgia LIKE ? OR ho_ten LIKE ? OR so_dien_thoai LIKE ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sss", $kw, $kw, $kw);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $list[] = $this->mapToModel($row);
        }
        return $list;
    }

    
    public function add(DocGiaModel $docgia) {
        $sql = "INSERT INTO docgia (ma_docgia, user_id, ho_ten, ngay_sinh, gioi_tinh, email, so_dien_thoai, dia_chi, anh_chan_dung, ghi_chu_vi_pham) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        
        $ma = $docgia->getMaDocGia();
        $user_id = $docgia->getUserId();
        $ho_ten = $docgia->getHoTen();
        $ngay_sinh = $docgia->getNgaySinh();
        $gioi_tinh = $docgia->getGioiTinh();
        $email = $docgia->getEmail();
        $sdt = $docgia->getSoDienThoai();
        $dia_chi = $docgia->getDiaChi();
        $anh = $docgia->getAnhChanDung();
        $ghi_chu = $docgia->getGhiChuViPham();

        $stmt->bind_param("sissssssss", $ma, $user_id, $ho_ten, $ngay_sinh, $gioi_tinh, $email, $sdt, $dia_chi, $anh, $ghi_chu);
        return $stmt->execute();
    }

    
    public function update(DocGiaModel $docgia) {
        $sql = "UPDATE docgia SET ho_ten=?, ngay_sinh=?, gioi_tinh=?, email=?, so_dien_thoai=?, dia_chi=?, anh_chan_dung=?, ghi_chu_vi_pham=? 
                WHERE ma_docgia=?";
        $stmt = $this->conn->prepare($sql);

        $ho_ten = $docgia->getHoTen();
        $ngay_sinh = $docgia->getNgaySinh();
        $gioi_tinh = $docgia->getGioiTinh();
        $email = $docgia->getEmail();
        $sdt = $docgia->getSoDienThoai();
        $dia_chi = $docgia->getDiaChi();
        $anh = $docgia->getAnhChanDung();
        $ghi_chu = $docgia->getGhiChuViPham();
        $ma = $docgia->getMaDocGia();

        $stmt->bind_param("sssssssss", $ho_ten, $ngay_sinh, $gioi_tinh, $email, $sdt, $dia_chi, $anh, $ghi_chu, $ma);
        return $stmt->execute();
    }

    
    public function delete($ma_docgia) {
        $sql = "DELETE FROM docgia WHERE ma_docgia = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $ma_docgia);
        return $stmt->execute();
    }

    public function checkTrung($username, $ma_dg) {
        $sql = "SELECT 
                    (SELECT COUNT(*) FROM user WHERE username = ?) as u_exists,
                    (SELECT COUNT(*) FROM docgia WHERE ma_docgia = ?) as dg_exists";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $username, $ma_dg);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Tạo tài khoản và hồ sơ độc giả (Transactional)
     */
    public function createWithAccount(DocGiaModel $docgia, $username, $password) {
        $this->conn->begin_transaction();
        try {
            // 1. Tạo User
            $sql_u = "INSERT INTO user (username, password, role) VALUES (?, ?, 2)";
            $stmt_u = $this->conn->prepare($sql_u);
            $stmt_u->bind_param("ss", $username, $password);
            if (!$stmt_u->execute()) throw new Exception("Không thể tạo tài khoản.");

            // 2. Tạo Độc giả
            $docgia->setUserId($this->conn->insert_id);
            if (!$this->add($docgia)) throw new Exception("Không thể tạo hồ sơ độc giả.");

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            throw $e;
        }
    }

    public function updatePassword($userId, $password) {
        $sql = "UPDATE user SET password = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $password, $userId);
        return $stmt->execute();
    }

    public function getPassword($userId) {
        $sql = "SELECT password FROM user WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result ? $result['password'] : null;
    }

    
    private function mapToModel($row) {
        return new DocGiaModel(
            $row['ma_docgia'], $row['user_id'], $row['ho_ten'], $row['ngay_sinh'], 
            $row['gioi_tinh'], $row['email'], $row['so_dien_thoai'], $row['dia_chi'], 
            $row['anh_chan_dung'], $row['ghi_chu_vi_pham']
        );
    }
}