<?php

class dg_PhieuDat
{
    public $ma_pdt;
    public $ma_docgia;
    public $ma_sach;

    public $ten_sach;
    public $image;
    public $ten_tg;

    public $so_luong_tong;
    public $so_luong_hien_tai;

    public $ngay_dat;
    public $trang_thai;

    public function __construct($data = [])
    {
        $this->ma_pdt = $data['ma_pdt'] ?? null;
        $this->ma_docgia = $data['ma_docgia'] ?? null;
        $this->ma_sach = $data['ma_sach'] ?? null;

        $this->ten_sach = $data['ten_sach'] ?? null;
        $this->image = $data['image'] ?? null;
        $this->ten_tg = $data['ten_tg'] ?? null;

        $this->so_luong_tong = $data['so_luong_tong'] ?? 0;
        $this->so_luong_hien_tai = $data['so_luong_hien_tai'] ?? 0;

        $this->ngay_dat = $data['ngay_dat'] ?? null;
        $this->trang_thai = $data['trang_thai'] ?? 0;
    }
}
