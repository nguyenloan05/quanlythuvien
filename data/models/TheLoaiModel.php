<?php
class TheLoaiModel
{
    private $ma_loai_sach;
    private $ten_loai_sach;

    public function __construct($ma = null, $ten = null)
    {
        $this->ma_loai_sach = $ma;
        $this->ten_loai_sach = $ten;
    }

    public function getMaLoaiSach()
    {
        return $this->ma_loai_sach;
    }

    public function setMaLoaiSach($ma)
    {
        $this->ma_loai_sach = $ma;
    }

    public function getTenLoaiSach()
    {
        return $this->ten_loai_sach;
    }

    public function setTenLoaiSach($ten)
    {
        $this->ten_loai_sach = $ten;
    }

    public function toArray()
    {
        return [
            'ma_loai_sach' => $this->ma_loai_sach,
            'ten_loai_sach' => $this->ten_loai_sach
        ];
    }
}
