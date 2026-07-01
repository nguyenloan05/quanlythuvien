<?php
require_once __DIR__ . '/../connect.php';
require_once __DIR__ . '/../models/TacGiaModel.php';

class TacGiaRepository
{
    private $conn;

    public function __construct()
    {
        $connect = new Database();
        $this->conn = $connect->getConnection();
    }

    
    public function getAll()
    {
        $sql = "SELECT * FROM tac_gia";
        $result = mysqli_query($this->conn, $sql);

        $list = [];
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $list[] = new TacGiaModel(
                    $row['ma_tg'],
                    $row['ten_tg'],
                    $row['gioi_tinh'],
                    $row['ngay_sinh'],
                    $row['que'],
                    $row['tieu_su'],
                    $row['hinh']
                );
            }
        }
        return $list;
    }

    
    public function getById($id)
    {
        $sql = "SELECT * FROM tac_gia WHERE ma_tg = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);

        if ($row) {
            return new TacGiaModel(
                $row['ma_tg'],
                $row['ten_tg'],
                $row['gioi_tinh'],
                $row['ngay_sinh'],
                $row['que'],
                $row['tieu_su'],
                $row['hinh']
            );
        }
        return null;
    }

    
    public function insert($tg)
    {
        $sql = "INSERT INTO tac_gia (ten_tg, gioi_tinh, ngay_sinh, que, tieu_su, hinh)
                VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($this->conn, $sql);

        $ten_tg   = $tg->getTenTG();
        $gioi_tinh = (int)$tg->getGioiTinh();
        $ngay_sinh = $tg->getNgaySinh();
        $que      = $tg->getQue();
        $tieu_su  = $tg->getTieuSu();
        $hinh     = $tg->getHinh();

        
        mysqli_stmt_bind_param(
            $stmt,
            "sissss",
            $ten_tg,
            $gioi_tinh,
            $ngay_sinh,
            $que,
            $tieu_su,
            $hinh
        );

        return mysqli_stmt_execute($stmt);
    }

    
    public function update($tg)
    {
        $sql = "UPDATE tac_gia 
                SET ten_tg=?, gioi_tinh=?, ngay_sinh=?, que=?, tieu_su=?, hinh=?
                WHERE ma_tg=?";

        $stmt = mysqli_prepare($this->conn, $sql);

        $ten_tg    = $tg->getTenTG();
        $gioi_tinh = (int)$tg->getGioiTinh();
        $ngay_sinh = $tg->getNgaySinh();
        $que       = $tg->getQue();
        $tieu_su   = $tg->getTieuSu();
        $hinh      = $tg->getHinh();
        $ma_tg     = $tg->getMaTG();

        
        mysqli_stmt_bind_param(
            $stmt,
            "sissssi",
            $ten_tg,
            $gioi_tinh,
            $ngay_sinh,
            $que,
            $tieu_su,
            $hinh,
            $ma_tg
        );

        return mysqli_stmt_execute($stmt);
    }

    
    public function delete($id)
    {
        $sql = "DELETE FROM tac_gia WHERE ma_tg=?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        return mysqli_stmt_execute($stmt);
    }

    
    public function search($keyword)
    {
        $sql = "SELECT * FROM tac_gia WHERE ten_tg LIKE ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        $like = "%$keyword%";
        mysqli_stmt_bind_param($stmt, "s", $like);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        $list = [];
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $list[] = new TacGiaModel(
                    $row['ma_tg'],
                    $row['ten_tg'],
                    $row['gioi_tinh'],
                    $row['ngay_sinh'],
                    $row['que'],
                    $row['tieu_su'],
                    $row['hinh']
                );
            }
        }
        return $list;
    }
    public function hasBooks($ma_tg)
    {
        $sql = "SELECT COUNT(*) as total FROM sach WHERE ma_tg = ?";
        $stmt = mysqli_prepare($this->conn, $sql);

        mysqli_stmt_bind_param($stmt, "i", $ma_tg);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);

        return $row['total'] > 0;
    }
}
