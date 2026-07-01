<?php

class TacGiaModel
{
    private $ma_tg;
    private $ten_tg;
    private $gioi_tinh;
    private $ngay_sinh;
    private $que;
    private $tieu_su;
    private $hinh;

    
    public function __construct($ma_tg = null, $ten_tg = null, $gioi_tinh = null, $ngay_sinh = null, $que = null, $tieu_su = null, $hinh = null)
    {
        $this->ma_tg = $ma_tg;
        $this->ten_tg = $ten_tg;
        $this->gioi_tinh = $gioi_tinh;
        $this->ngay_sinh = $ngay_sinh;
        $this->que = $que;
        $this->tieu_su = $tieu_su;
        $this->hinh = $hinh;
    }

    

    public function getMaTG()
    {
        return $this->ma_tg;
    }

    public function setMaTG($ma_tg)
    {
        $this->ma_tg = $ma_tg;
    }

    public function getTenTG()
    {
        return $this->ten_tg;
    }

    public function setTenTG($ten_tg)
    {
        $this->ten_tg = $ten_tg;
    }

    public function getGioiTinh()
    {
        return $this->gioi_tinh;
    }

    public function setGioiTinh($gioi_tinh)
    {
        $this->gioi_tinh = $gioi_tinh;
    }

    public function getNgaySinh()
    {
        return $this->ngay_sinh;
    }

    public function setNgaySinh($ngay_sinh)
    {
        $this->ngay_sinh = $ngay_sinh;
    }

    public function getQue()
    {
        return $this->que;
    }

    public function setQue($que)
    {
        $this->que = $que;
    }

    public function getTieuSu()
    {
        return $this->tieu_su;
    }

    public function setTieuSu($tieu_su)
    {
        $this->tieu_su = $tieu_su;
    }

    public function getHinh()
    {
        return $this->hinh;
    }

    public function setHinh($hinh)
    {
        $this->hinh = $hinh;
    }

    public function toArray()
    {
        return [
            'ma_tg'     => $this->ma_tg,
            'ten_tg'    => $this->ten_tg,
            'gioi_tinh' => $this->gioi_tinh,
            'ngay_sinh' => $this->ngay_sinh,
            'que'       => $this->que,
            'tieu_su'   => $this->tieu_su,
            'hinh'      => $this->hinh,
        ];
    }
}
