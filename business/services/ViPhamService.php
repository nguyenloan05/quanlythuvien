<?php
class ViPhamService {
    private $repo;

    public function __construct($repo) {
        $this->repo = $repo;
    }

    public function tinhToanViPham($ma_pm, $tien_phat_them = 0) {
        $info = $this->repo->getThongTinPM($ma_pm);
        if (!$info) return null;

        $han_tra = new DateTime($info['ngay_tra_du_kien']);
        $ngay_tra = new DateTime(); 
        
        $so_ngay_tre = 0;
        $tien_phat_tre = 0;

        if ($ngay_tra > $han_tra) {
            $diff = $ngay_tra->diff($han_tra);
            $so_ngay_tre = $diff->days;
            
            $tien_phat_tre = $so_ngay_tre * $info['so_luong'] * 5000;
        }

        return [
            'so_ngay_tre' => $so_ngay_tre,
            'tien_phat_tre' => $tien_phat_tre,
            'tien_phat_them' => $tien_phat_them,
            'tong_tien_phat' => $tien_phat_tre + $tien_phat_them,
            'data' => $info
        ];
    }
}
?>