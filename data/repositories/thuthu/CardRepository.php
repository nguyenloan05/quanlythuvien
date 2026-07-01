<?php
require_once __DIR__ . "/../../connect.php";
require_once __DIR__ . '/../../models/Card.php';

class CardRepository
{
    private $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function checkCodeExists($ma_the)
    {
        $sql = "SELECT id FROM the_thu_vien WHERE ma_the = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $ma_the);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }

    public function activate($id, $ma_the)
    {
        $sql = "UPDATE the_thu_vien 
            SET ma_the = ?, trang_thai = 1,
                ngay_cap = NOW(),
                ly_do_khoa = NULL,
                ngay_het_han = DATE_ADD(NOW(), INTERVAL 1 YEAR)
            WHERE id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $ma_the, $id);
        return $stmt->execute();
    }

    public function createRegistration($ma_docgia)
    {
        $sqlCheck = "SELECT trang_thai 
                 FROM the_thu_vien 
                 WHERE ma_docgia = ? 
                 ORDER BY id DESC 
                 LIMIT 1";

        $stmt = $this->conn->prepare($sqlCheck);
        $stmt->bind_param("s", $ma_docgia);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if ($result) {
            if ($result['trang_thai'] == 0 || $result['trang_thai'] == 1) {
                return false;
            }
        }

        $sql = "INSERT INTO the_thu_vien (ma_docgia, trang_thai, ma_the)
                VALUES (?, 0, '')";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $ma_docgia);

        if (!$stmt->execute()) {
            return false;
        }
        return true;
    }

    public function getByMaDG($ma_docgia)
    {
        $sql = "SELECT t.*
        FROM the_thu_vien t
        WHERE t.ma_docgia = ?
        ORDER BY t.id DESC
        LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $ma_docgia);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result ? new Card($result) : null;
    }

    public function getById($id)
    {
        $sql = "SELECT t.*, d.ho_ten, d.email, d.anh_chan_dung 
            FROM the_thu_vien t
            JOIN docgia d ON d.ma_docgia = t.ma_docgia
            WHERE t.id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            return null;
        }

        return new Card($result->fetch_assoc());
    }

    public function getAll()
    {
        $sql = "SELECT t.ma_docgia, d.ho_ten, d.email, d.anh_chan_dung,
                   t.id, t.ma_the, t.ngay_cap, t.ngay_het_han,
                   t.trang_thai, t.ly_do_khoa
            FROM the_thu_vien t
            JOIN docgia d ON d.ma_docgia = t.ma_docgia
            ORDER BY t.id DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        $result = $stmt->get_result();

        $data = [];

        while ($row = $result->fetch_assoc()) {
            $data[] = new Card($row);
        }

        return $data;
    }

    public function lock($id, $ly_do_khoa)
    {
        $sql = "UPDATE the_thu_vien 
            SET trang_thai = 2, ly_do_khoa = ?
            WHERE id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $ly_do_khoa, $id);

        return $stmt->execute();
    }

    public function unlock($id)
    {
        $sql = "UPDATE the_thu_vien 
            SET trang_thai = 1, ly_do_khoa = NULL
            WHERE id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
