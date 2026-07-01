<?php
require_once __DIR__ . '/../connect.php';
require_once __DIR__ . '/../models/SachModel.php';

class SachRepository
{
    private $conn;

    public function __construct()
    {
        $connect = new Database();
        $this->conn = $connect->getConnection();
    }

    
    public function getAll()
    {
        $sql = "SELECT s.*, ls.ten_loai_sach, tg.ten_tg
            FROM sach s
            JOIN loai_sach ls ON s.ma_loai_sach = ls.ma_loai_sach
            JOIN tac_gia tg ON s.ma_tg = tg.ma_tg";
        $result = mysqli_query($this->conn, $sql);

        $list = [];
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $list[] = new SachModel(
                    $row['ma_sach'],
                    $row['ten_sach'],
                    $row['ma_loai_sach'],
                    $row['nha_xb'],
                    $row['nam_xb'],
                    $row['tinh_trang'],
                    $row['mo_ta'],
                    $row['image'],
                    $row['ma_tg'],
                    $row['so_luong_tong'],
                    $row['so_luong_hien_tai'],
                    $row['vi_tri'],
                    $row['ten_loai_sach'],   
                    $row['ten_tg']
                );
            }
        }
        return $list;
    }

    
    public function getById($id)
    {
        $sql = "SELECT s.*, ls.ten_loai_sach, tg.ten_tg
            FROM sach s
            LEFT JOIN loai_sach ls ON s.ma_loai_sach = ls.ma_loai_sach
            LEFT JOIN tac_gia tg ON s.ma_tg = tg.ma_tg
            WHERE s.ma_sach = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);

        if ($row) {
            return new SachModel(
                $row['ma_sach'],
                $row['ten_sach'],
                $row['ma_loai_sach'],
                $row['nha_xb'],
                $row['nam_xb'],
                $row['tinh_trang'],
                $row['mo_ta'],
                $row['image'],
                $row['ma_tg'],
                $row['so_luong_tong'],
                $row['so_luong_hien_tai'],
                $row['vi_tri'],
                $row['ten_loai_sach'] ?? null,
                $row['ten_tg'] ?? null
            );
        }
        return null;
    }


    
    public function insert($sach)
    {
        $sql = "INSERT INTO sach 
        (ten_sach, ma_loai_sach, nha_xb, nam_xb, tinh_trang, mo_ta, image, ma_tg, so_luong_tong, so_luong_hien_tai, vi_tri)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($this->conn, $sql);

        $ten_sach = $sach->getTenSach();
        $ma_loai = $sach->getMaLoaiSach();
        $nha_xb = $sach->getNhaXB();
        $nam_xb = (int)$sach->getNamXB();
        $tinh_trang = (int)$sach->getTinhTrang();
        $mo_ta = $sach->getMoTa();
        $image = $sach->getImage();
        $ma_tg = (int)$sach->getMaTG();
        $so_luong_tong = (int)$sach->getSoLuongTong();
        $so_luong_hien_tai = (int)$sach->getSoLuongHienTai();
        $vi_tri = $sach->getViTri();

        mysqli_stmt_bind_param(
            $stmt,
            "sssiissiiis",
            $ten_sach,
            $ma_loai,
            $nha_xb,
            $nam_xb,
            $tinh_trang,
            $mo_ta,
            $image,
            $ma_tg,
            $so_luong_tong,
            $so_luong_hien_tai,
            $vi_tri
        );

        return mysqli_stmt_execute($stmt);
    }

    
    public function update($sach)
    {
        $sql = "UPDATE sach SET 
            ten_sach=?, 
            ma_loai_sach=?, 
            nha_xb=?, 
            nam_xb=?, 
            tinh_trang=?, 
            mo_ta=?, 
            image=?, 
            ma_tg=?, 
            so_luong_tong=?, 
            so_luong_hien_tai=?, 
            vi_tri=?
            WHERE ma_sach=?";

        $stmt = mysqli_prepare($this->conn, $sql);

        $ten_sach = $sach->getTenSach();
        $ma_loai = $sach->getMaLoaiSach();
        $nha_xb = $sach->getNhaXB();
        $nam_xb = (int)$sach->getNamXB();
        $tinh_trang = (int)$sach->getTinhTrang();
        $mo_ta = $sach->getMoTa();
        $image = $sach->getImage();
        $ma_tg = (int)$sach->getMaTG();
        $so_luong_tong = (int)$sach->getSoLuongTong();
        $so_luong_hien_tai = (int)$sach->getSoLuongHienTai();
        $vi_tri = $sach->getViTri();
        $ma_sach = (int)$sach->getMaSach();

        mysqli_stmt_bind_param(
            $stmt,
            "sssisssiiisi",
            $ten_sach,
            $ma_loai,
            $nha_xb,
            $nam_xb,
            $tinh_trang,
            $mo_ta,
            $image,
            $ma_tg,
            $so_luong_tong,
            $so_luong_hien_tai,
            $vi_tri,
            $ma_sach
        );

        return mysqli_stmt_execute($stmt);
    }

    public function delete($id)
    {
        $sql = "DELETE FROM sach WHERE ma_sach=?";
        $stmt = mysqli_prepare($this->conn, $sql);

        if (!$stmt) {
            throw new Exception("SQL lỗi: " . mysqli_error($this->conn));
        }

        mysqli_stmt_bind_param($stmt, "i", $id);

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Lỗi xoá: " . mysqli_stmt_error($stmt));
        }

        if (mysqli_stmt_affected_rows($stmt) === 0) {
            return false; 
        }

        return true;
    }

    public function search($keyword)
    {
        $keyword = strtolower(trim($keyword));

        $sql = "SELECT s.*, ls.ten_loai_sach, tg.ten_tg
            FROM sach s
            LEFT JOIN loai_sach ls ON s.ma_loai_sach = ls.ma_loai_sach
            LEFT JOIN tac_gia tg ON s.ma_tg = tg.ma_tg
            WHERE LOWER(s.ten_sach) LIKE ?";

        $stmt = mysqli_prepare($this->conn, $sql);

        $like = "%$keyword%";
        mysqli_stmt_bind_param($stmt, "s", $like);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        $list = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $list[] = new SachModel(
                $row['ma_sach'],
                $row['ten_sach'],
                $row['ma_loai_sach'],
                $row['nha_xb'],
                $row['nam_xb'],
                $row['tinh_trang'],
                $row['mo_ta'],
                $row['image'],
                $row['ma_tg'],
                $row['so_luong_tong'],
                $row['so_luong_hien_tai'],
                $row['vi_tri'],
                $row['ten_loai_sach'] ?? 'Chưa rõ thể loại',
                $row['ten_tg'] ?? 'Chưa rõ tác giả'
            );
        }

        return $list;
    }


    public function getByTenSach($tenSach)
    {
        $sql = "SELECT * FROM sach WHERE ten_sach = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $tenSach);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);

        if ($row) {
            return new SachModel(
                $row['ma_sach'],
                $row['ten_sach'],
                $row['ma_loai_sach'],
                $row['nha_xb'],
                $row['nam_xb'],
                $row['tinh_trang'],
                $row['mo_ta'],
                $row['image'],
                $row['ma_tg'],
                $row['so_luong_tong'],
                $row['so_luong_hien_tai'],
                $row['vi_tri']
            );
        }
        return null;
    }
    public function isSachTrongPhieuMuon($id)
    {
        $sql = "SELECT COUNT(*) as total FROM phieu_muon WHERE ma_sach = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);

        return $row['total'] > 0;
    }
}
