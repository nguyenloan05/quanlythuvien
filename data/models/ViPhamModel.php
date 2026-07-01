<?php
class ViPhamModel {
    public $ma_vp; 
    public $ma_docgia;
    public $ly_do;
    public $ngay_vp; 
    public $hinh_thuc;
    public $trang_thai; 
    public $tien_phat_them; 
    public $tong_tien_phat; 
    public $ma_pm;

    public function __construct($data = []) {
        $this->ma_vp = $data['ma_vp'] ?? null;
        $this->ma_docgia = $data['ma_docgia'] ?? '';
        $this->ma_pm = $data['ma_pm'] ?? '';
        $this->ly_do = $data['ly_do'] ?? '';
        
        
        $this->ngay_vp = $data['ngay_vp'] ?? date('Y-m-d');
        
        $this->hinh_thuc = $data['hinh_thuc'] ?? 'Phạt tiền';
        
        
        $this->trang_thai = $data['trang_thai'] ?? '0'; 
        $this->tien_phat_them = isset($data['tien_phat_them']) ? (float)$data['tien_phat_them'] : 0.0;
        $this->tong_tien_phat = isset($data['tong_tien_phat']) ? (int)$data['tong_tien_phat'] : 0;
    }
}
?>