<?php

require_once __DIR__ . '/../../connect.php';
require_once __DIR__ . '/../../models/PhieuDat.php';

class PhieuDatRepository
{
    private $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function getAll()
    {
        $sql = "
            SELECT p.*, s.ten_sach, s.image, s.so_luong_hien_tai, d.ho_ten
            FROM phieu_dat_truoc p
            JOIN sach s ON p.ma_sach = s.ma_sach
            JOIN docgia d ON p.ma_docgia = d.ma_docgia
            ORDER BY p.ngay_dat DESC
        ";

        $res = mysqli_query($this->conn, $sql);
        $data = [];

        if ($res) {
            while ($row = mysqli_fetch_assoc($res)) {
                $data[] = new PhieuDat($row);
            }
        }

        return $data;
    }
    
    public function getById($id)
    {
        $sql = "
        SELECT p.*, s.so_luong_hien_tai
        FROM phieu_dat_truoc p
        JOIN sach s ON p.ma_sach = s.ma_sach
        WHERE p.ma_pdt = ?
    ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    public function updateStatus($id, $status)
    {
        $sql = "UPDATE phieu_dat_truoc SET trang_thai = ? WHERE ma_pdt = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $status, $id);

        return $stmt->execute();
    }

    public function decreaseStock($ma_sach)
    {
        $sql = "UPDATE sach SET so_luong_hien_tai = so_luong_hien_tai - 1 WHERE ma_sach = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $ma_sach);

        return $stmt->execute();
    }
//-----
    public function updateStatusApprove($id, $han_lay_sach)
    {
        $sql = "
        UPDATE phieu_dat_truoc
        SET trang_thai = 1,
            han_lay_sach = ?
        WHERE ma_pdt = ?
    ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $han_lay_sach, $id);

        return $stmt->execute();
    }

    public function reject($id)
    {
        $sql = "
        UPDATE phieu_dat_truoc
        SET 
            trang_thai = 3,
            han_lay_sach = NULL
        WHERE ma_pdt = ?
    ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);

        return $stmt->execute();
    }

    public function updateReceived($id, $han_lay_sach)
    {
        $sql = "
        UPDATE phieu_dat_truoc
        SET 
            trang_thai = 2,
            han_lay_sach = ?
        WHERE ma_pdt = ?
    ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $han_lay_sach, $id);

        return $stmt->execute();
    }
}

