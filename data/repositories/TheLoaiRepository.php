<?php
require_once __DIR__ . '/../connect.php';
require_once __DIR__ . '/../models/TheLoaiModel.php';

class TheLoaiRepository
{
    private $conn;

    public function __construct()
    {
        $connect = new Database();
        $this->conn = $connect->getConnection();
    }

    
    public function getAll()
    {
        $sql = "SELECT * FROM loai_sach";
        $result = mysqli_query($this->conn, $sql);

        $list = [];
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $list[] = new TheLoaiModel(
                    $row['ma_loai_sach'],
                    $row['ten_loai_sach']
                );
            }
        }
        return $list;
    }

    
    public function getById($id)
    {
        $sql = "SELECT * FROM loai_sach WHERE ma_loai_sach = ?";
        $stmt = mysqli_prepare($this->conn, $sql);

        mysqli_stmt_bind_param($stmt, "s", $id);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);

        if ($row) {
            return new TheLoaiModel(
                $row['ma_loai_sach'],
                $row['ten_loai_sach']
            );
        }
        return null;
    }

    
    public function insert($ls)
    {
        $sql = "INSERT INTO loai_sach (ma_loai_sach, ten_loai_sach)
                VALUES (?, ?)";

        $stmt = mysqli_prepare($this->conn, $sql);

        $ma  = $ls->getMaLoaiSach();
        $ten = $ls->getTenLoaiSach();

        mysqli_stmt_bind_param($stmt, "ss", $ma, $ten);

        return mysqli_stmt_execute($stmt);
    }

    
    public function update($ls)
    {
        $sql = "UPDATE loai_sach 
                SET ten_loai_sach=? 
                WHERE ma_loai_sach=?";

        $stmt = mysqli_prepare($this->conn, $sql);

        $ma  = $ls->getMaLoaiSach();
        $ten = $ls->getTenLoaiSach();

        mysqli_stmt_bind_param($stmt, "ss", $ten, $ma);

        return mysqli_stmt_execute($stmt);
    }

    
    public function delete($id)
    {
        $sql = "DELETE FROM loai_sach WHERE ma_loai_sach=?";
        $stmt = mysqli_prepare($this->conn, $sql);

        mysqli_stmt_bind_param($stmt, "s", $id);
        return mysqli_stmt_execute($stmt);
    }
    
    public function hasBooks(string $ma_loai): bool
    {
        $sql = "SELECT COUNT(*) AS total FROM sach WHERE ma_loai_sach = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $ma_loai);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);

        return $row && $row['total'] > 0;
    }
}
