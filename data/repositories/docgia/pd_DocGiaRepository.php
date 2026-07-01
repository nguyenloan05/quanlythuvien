<?php
require_once __DIR__ . '/../../../data/connect.php';
require_once __DIR__ . '/../../models/dg_PhieuDat.php';

class pd_DocGiaRepository
{
    private $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function getMaDocGiaByUserId($user_id)
    {
        $sql = "SELECT ma_docgia FROM docgia WHERE user_id = ? LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();

        $res = $stmt->get_result()->fetch_assoc();

        return $res['ma_docgia'] ?? null;
    }

    public function searchSach($keyword)
    {
        $sql = "
        SELECT 
            s.ma_sach, s.ten_sach, s.image,
            s.so_luong_tong, s.so_luong_hien_tai,
            tg.ten_tg
        FROM sach s
        LEFT JOIN tac_gia tg ON s.ma_tg = tg.ma_tg
    ";

        if (!empty($keyword)) {
            $sql .= " WHERE s.ten_sach LIKE ? OR tg.ten_tg LIKE ? ";
        }

        $sql .= " ORDER BY s.ma_sach DESC";

        $stmt = $this->conn->prepare($sql);

        if (!empty($keyword)) {
            $kw = "%$keyword%";
            $stmt->bind_param("ss", $kw, $kw);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        $data = [];

        while ($row = $result->fetch_assoc()) {
            $model = new dg_PhieuDat();
            $model->ma_sach = $row['ma_sach'];
            $model->ten_sach = $row['ten_sach'];
            $model->image = $row['image'];
            $model->ten_tg = $row['ten_tg'];
            $model->so_luong_tong = $row['so_luong_tong'];
            $model->so_luong_hien_tai = $row['so_luong_hien_tai'];

            $data[] = $model;
        }

        return $data;
    }

    public function checkSoLuong($ma_sach)
    {
        $sql = "
            SELECT so_luong_hien_tai
            FROM sach
            WHERE ma_sach = ?
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $ma_sach);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    public function insertPhieu($ma_docgia, $ma_sach)
    {
        $sql = "
        INSERT INTO phieu_dat_truoc
        (ma_docgia, ma_sach, ngay_dat, trang_thai)
        VALUES (?, ?, NOW(), 0)
    ";

        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            die("Prepare lỗi: " . $this->conn->error);
        }

        $stmt->bind_param("si", $ma_docgia, $ma_sach);

        if (!$stmt->execute()) {
            return false;
        }

        return true;
    }

    public function giamSoLuong($ma_sach)
    {
        $sql = "
            UPDATE sach
            SET so_luong_hien_tai = so_luong_hien_tai - 1
            WHERE ma_sach = ? AND so_luong_hien_tai > 0
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $ma_sach);

        return $stmt->execute();
    }

    public function tangSoLuong($ma_sach)
    {
        $sql = "
            UPDATE sach
            SET so_luong_hien_tai = so_luong_hien_tai + 1
            WHERE ma_sach = ?
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $ma_sach);

        return $stmt->execute();
    }

    public function daDat($ma_docgia, $ma_sach)
    {
        $sql = "
        SELECT 1 FROM phieu_dat_truoc
        WHERE ma_docgia = ? AND ma_sach = ?
        AND trang_thai IN (0,1)
        LIMIT 1
    ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $ma_docgia, $ma_sach);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc() ? true : false;
    }

    public function checkDaDat($ma_docgia, $ma_sach)
    {
        $sql = "SELECT * FROM phieu_dat_truoc 
            WHERE ma_docgia = ? AND ma_sach = ? AND trang_thai IN (0,1)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $ma_docgia, $ma_sach);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    public function getTrangThai($ma_docgia, $ma_sach)
    {
        $sql = "
        SELECT trang_thai
        FROM phieu_dat_truoc
        WHERE ma_docgia = ?
        AND ma_sach = ?
        ORDER BY ma_pdt DESC
        LIMIT 1
    ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $ma_docgia, $ma_sach);
        $stmt->execute();

        $res = $stmt->get_result()->fetch_assoc();

        return $res['trang_thai'] ?? -1;
    }

    public function updateTrangThai($ma_pdt, $trang_thai)
    {
        $sql = "
        UPDATE phieu_dat_truoc
        SET trang_thai = ?
        WHERE ma_pdt = ?
    ";

        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            die("Prepare lỗi: " . $this->conn->error);
        }

        $stmt->bind_param("ii", $trang_thai, $ma_pdt);

        return $stmt->execute();
    }

    public function getCardStatus($ma_docgia)
    {
        $sql = "SELECT trang_thai FROM the_thu_vien WHERE ma_docgia = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $ma_docgia);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        return $res ? (int)$res['trang_thai'] : null;
    }
}
