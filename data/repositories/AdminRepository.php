<?php
class AdminRepository {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    
    public function getThongKeDashboard() {
        $data = [];

        
        $res = $this->conn->query("SELECT COUNT(*) as total FROM docgia");
        $data['tong_doc_gia'] = $res ? $res->fetch_assoc()['total'] : 0;

        
        $data['the_hoat_dong'] = $this->conn->query("SELECT COUNT(*) FROM the_thu_vien WHERE trang_thai='Da_Kich_Hoat'")->fetch_row()[0] ?? 0;
        $data['the_cho_duyet'] = $this->conn->query("SELECT COUNT(*) FROM the_thu_vien WHERE trang_thai='Cho_Xac_Nhan'")->fetch_row()[0] ?? 0;
        $data['the_bi_khoa']   = $this->conn->query("SELECT COUNT(*) FROM the_thu_vien WHERE trang_thai='Bi_Khoa'")->fetch_row()[0] ?? 0;

        
        $res_phat = $this->conn->query("SELECT SUM(tien_phat) as total FROM hinh_phat");
        $data['tong_phi_phat'] = $res_phat ? $res_phat->fetch_assoc()['total'] : 0;

        
        $top_sach = [];
        $sql_top = "SELECT s.ten_sach, tg.ten_tg, s.image, COUNT(m.ma_sach) as luot_muon 
                    FROM sach s 
                    JOIN tac_gia tg ON s.ma_tg = tg.ma_tg 
                    LEFT JOIN phieu_muon m ON s.ma_sach = m.ma_sach 
                    GROUP BY s.ma_sach 
                    ORDER BY luot_muon DESC LIMIT 5";
        $res_top = $this->conn->query($sql_top);
        
        if ($res_top && $res_top->num_rows > 0) {
            while ($row = $res_top->fetch_assoc()) {
                $top_sach[] = $row;
            }
        }
        $data['top_sach'] = $top_sach;

        return $data;
    }
}
?>