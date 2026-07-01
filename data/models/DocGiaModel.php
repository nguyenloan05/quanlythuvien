<?php
class DocGiaModel {
    private $ma_docgia;
    private $user_id;
    private $ho_ten;
    private $ngay_sinh;
    private $gioi_tinh;
    private $email;
    private $so_dien_thoai;
    private $dia_chi;
    private $anh_chan_dung;
    private $ghi_chu_vi_pham;

    public function __construct($ma_docgia = null, $user_id = null, $ho_ten = '', $ngay_sinh = null, $gioi_tinh = '', $email = '', $so_dien_thoai = '', $dia_chi = '', $anh_chan_dung = '', $ghi_chu_vi_pham = null) {
        $this->ma_docgia = $ma_docgia;
        $this->user_id = $user_id;
        $this->ho_ten = $ho_ten;
        $this->ngay_sinh = $ngay_sinh;
        $this->gioi_tinh = $gioi_tinh;
        $this->email = $email;
        $this->so_dien_thoai = $so_dien_thoai;
        $this->dia_chi = $dia_chi;
        $this->anh_chan_dung = $anh_chan_dung;
        $this->ghi_chu_vi_pham = $ghi_chu_vi_pham;
    }

    
    public function getMaDocGia() { return $this->ma_docgia; }
    public function getUserId() { return $this->user_id; }
    public function getHoTen() { return $this->ho_ten; }
    public function getNgaySinh() { return $this->ngay_sinh; }
    public function getGioiTinh() { return $this->gioi_tinh; }
    public function getEmail() { return $this->email; }
    public function getSoDienThoai() { return $this->so_dien_thoai; }
    public function getDiaChi() { return $this->dia_chi; }
    public function getAnhChanDung() { return $this->anh_chan_dung; }
    public function getGhiChuViPham() { return $this->ghi_chu_vi_pham; }

    
    public function setAnhChanDung($anh) { $this->anh_chan_dung = $anh; }
    public function setUserId($id) { $this->user_id = $id; }

    
    public function toArray() {
        return [
            "ma_docgia" => $this->ma_docgia,
            "user_id" => $this->user_id,
            "ho_ten" => $this->ho_ten,
            "ngay_sinh" => $this->ngay_sinh,
            "gioi_tinh" => $this->gioi_tinh,
            "email" => $this->email,
            "so_dien_thoai" => $this->so_dien_thoai,
            "dia_chi" => $this->dia_chi,
            "anh_chan_dung" => $this->anh_chan_dung,
            "ghi_chu_vi_pham" => $this->ghi_chu_vi_pham
        ];
    }
}