<?php
class PhieuMuonModel {
    
    public $ma_pm;              
    public $ma_docgia;          
    public $ma_sach;            
    public $tinh_trang;         
    public $ngay_muon;          
    public $ngay_tra_du_kien;   
    public $so_luong;           
    public $ngay_tra_thuc_te;   
    public $hinh_thuc;          
    public $ma_thu_thu_duyet;   

    
    public $ten_sach;
    public $ten_docgia;
    public $ten_thu_thu;

    public function __construct($data = []) {
        $this->ma_pm = $data['ma_pm'] ?? null;
        $this->ma_docgia = $data['ma_docgia'] ?? null;
        $this->ma_sach = isset($data['ma_sach']) ? (int)$data['ma_sach'] : null;
        
        
        $this->tinh_trang = isset($data['tinh_trang']) ? (int)$data['tinh_trang'] : 0;
        $this->hinh_thuc = isset($data['hinh_thuc']) ? (int)$data['hinh_thuc'] : 0;
        
        $this->ngay_muon = $data['ngay_muon'] ?? null;
        $this->ngay_tra_du_kien = $data['ngay_tra_du_kien'] ?? null;
        $this->so_luong = isset($data['so_luong']) ? (int)$data['so_luong'] : 1;
        $this->ngay_tra_thuc_te = $data['ngay_tra_thuc_te'] ?? null;
        $this->ma_thu_thu_duyet = isset($data['ma_thu_thu_duyet']) ? (int)$data['ma_thu_thu_duyet'] : null;

        
        $this->ten_sach = $data['ten_sach'] ?? null;
        $this->ten_docgia = $data['ho_ten'] ?? null; 
        $this->ten_thu_thu = $data['ten_thu_thu'] ?? null; 
    }

    public function getTenTrangThai() {
        $labels = [
            0 => "Chờ duyệt",
            1 => "Đang mượn",
            2 => "Đã trả",
            3 => "Vi phạm",
            4 => "Đã hủy"
        ];
        return $labels[$this->tinh_trang] ?? "Không xác định";
    }

    
    public function getTenHinhThuc() {
        return ($this->hinh_thuc === 1) ? "Tại quầy" : "Online";
    }

    
    public function isQuaHan() {
        if ($this->tinh_trang === 1 && !empty($this->ngay_tra_du_kien)) {
            $today = date('Y-m-d');
            return ($today > $this->ngay_tra_du_kien);
        }
        return false;
    }

    
    public function toArray() {
        return [
            'ma_pm' => $this->ma_pm,
            'ma_docgia' => $this->ma_docgia,
            'ma_sach' => $this->ma_sach,
            'ten_sach' => $this->ten_sach,
            'ten_dg' => $this->ten_docgia,
            'tinh_trang' => $this->tinh_trang,
            'ngay_muon' => $this->ngay_muon,
            'ngay_tra_du_kien' => $this->ngay_tra_du_kien,
            'so_luong' => $this->so_luong,
            'ngay_tra_thuc_te' => $this->ngay_tra_thuc_te,
            'hinh_thuc' => $this->hinh_thuc,
            'ma_thu_thu_duyet' => $this->ma_thu_thu_duyet
        ];
    }
}