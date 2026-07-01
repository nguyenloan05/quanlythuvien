<?php
class PhieuDat
{

    public $ma_pdt;
    public $ma_docgia;
    public $ma_sach;
    public $ngay_dat;
    public $han_lay_sach; //
    public $trang_thai;   

    public $ten_sach;
    public $image;
    public $ho_ten;
    public $so_luong_hien_tai;

    public function __construct($data = [])
    {
        $this->ma_pdt = $data['ma_pdt'] ?? null;
        $this->ma_docgia = $data['ma_docgia'] ?? null;
        $this->ma_sach = $data['ma_sach'] ?? null;
        $this->ngay_dat = $data['ngay_dat'] ?? null;
        $this->han_lay_sach = $data['han_lay_sach'] ?? null;
        $this->trang_thai = isset($data['trang_thai']) ? (int)$data['trang_thai'] : 0;

        $this->ten_sach = $data['ten_sach'] ?? '';
        $this->image = $data['image'] ?? '';
        $this->ho_ten = $data['ho_ten'] ?? '';
        $this->so_luong_hien_tai = isset($data['so_luong_hien_tai']) ? (int)$data['so_luong_hien_tai'] : 0;
    }

    public function toArray()
    {
        return [
            "ma_pdt" => $this->ma_pdt,
            "ma_docgia" => $this->ma_docgia,
            "ma_sach" => $this->ma_sach,
            "ngay_dat" => $this->ngay_dat,
            "han_lay_sach" => $this->han_lay_sach,
            "trang_thai" => $this->trang_thai,
            "ten_sach" => $this->ten_sach,
            "image" => $this->image,
            "ho_ten" => $this->ho_ten,
            "so_luong_hien_tai" => $this->so_luong_hien_tai
        ];
    }
}
