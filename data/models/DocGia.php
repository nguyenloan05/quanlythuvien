<?php
class DocGia
{
    public $ma_docgia;
    public $user_id;
    public $ho_ten;
    public $ngay_sinh;
    public $gioi_tinh;
    public $email;
    public $so_dien_thoai;
    public $dia_chi;
    public $anh_chan_dung;
    public $ghi_chu_vi_pham;

    public function __construct($data = [])
    {
        $this->ma_docgia = $data['ma_docgia'] ?? null;
        $this->user_id = $data['user_id'] ?? null;
        $this->ho_ten = $data['ho_ten'] ?? null;
        $this->ngay_sinh = $data['ngay_sinh'] ?? null;
        $this->gioi_tinh = $data['gioi_tinh'] ?? null;
        $this->email = $data['email'] ?? null;
        $this->so_dien_thoai = $data['so_dien_thoai'] ?? null;
        $this->dia_chi = $data['dia_chi'] ?? null;
        $this->anh_chan_dung = $data['anh_chan_dung'] ?? 'default_avatar.png';
        $this->ghi_chu_vi_pham = $data['ghi_chu_vi_pham'] ?? null;
    }

    public function toArray()
    {
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
