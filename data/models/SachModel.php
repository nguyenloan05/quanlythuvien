<?php

class SachModel
{
    private $ma_sach;
    private $ten_sach;
    private $ma_loai_sach;
    private $nha_xb;
    private $nam_xb;
    private $tinh_trang;
    private $mo_ta;
    private $image;
    private $ma_tg;
    private $so_luong_tong;
    private $so_luong_hien_tai;
    private $vi_tri;
    private $ten_loai_sach;
    private $ten_tg;

    
    public function __construct(
        $ma_sach = null,
        $ten_sach = null,
        $ma_loai_sach = null,
        $nha_xb = null,
        $nam_xb = null,
        $tinh_trang = null,
        $mo_ta = null,
        $image = null,
        $ma_tg = null,
        $so_luong_tong = null,
        $so_luong_hien_tai = null,
        $vi_tri = null,
        $ten_loai_sach = null,
        $ten_tg = null
    ) {
        $this->ma_sach = $ma_sach;
        $this->ten_sach = $ten_sach;
        $this->ma_loai_sach = $ma_loai_sach;
        $this->nha_xb = $nha_xb;
        $this->nam_xb = $nam_xb;
        $this->tinh_trang = $tinh_trang;
        $this->mo_ta = $mo_ta;
        $this->image = $image;
        $this->ma_tg = $ma_tg;
        $this->so_luong_tong = $so_luong_tong;
        $this->so_luong_hien_tai = $so_luong_hien_tai;
        $this->vi_tri = $vi_tri;
        $this->ten_loai_sach = $ten_loai_sach;
        $this->ten_tg = $ten_tg;
    }

    

    public function getMaSach()
    {
        return $this->ma_sach;
    }

    public function setMaSach($ma_sach)
    {
        $this->ma_sach = $ma_sach;
    }

    public function getTenSach()
    {
        return $this->ten_sach;
    }

    public function setTenSach($ten_sach)
    {
        $this->ten_sach = $ten_sach;
    }

    public function getMaLoaiSach()
    {
        return $this->ma_loai_sach;
    }

    public function setMaLoaiSach($ma_loai_sach)
    {
        $this->ma_loai_sach = $ma_loai_sach;
    }

    public function getNhaXB()
    {
        return $this->nha_xb;
    }

    public function setNhaXB($nha_xb)
    {
        $this->nha_xb = $nha_xb;
    }

    public function getNamXB()
    {
        return $this->nam_xb;
    }

    public function setNamXB($nam_xb)
    {
        $this->nam_xb = $nam_xb;
    }

    public function getTinhTrang()
    {
        return $this->tinh_trang;
    }

    public function setTinhTrang($tinh_trang)
    {
        $this->tinh_trang = $tinh_trang;
    }

    public function getMoTa()
    {
        return $this->mo_ta;
    }

    public function setMoTa($mo_ta)
    {
        $this->mo_ta = $mo_ta;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function setImage($image)
    {
        $this->image = $image;
    }

    public function getMaTG()
    {
        return $this->ma_tg;
    }

    public function setMaTG($ma_tg)
    {
        $this->ma_tg = $ma_tg;
    }

    public function getSoLuongTong()
    {
        return $this->so_luong_tong;
    }

    public function setSoLuongTong($so_luong_tong)
    {
        $this->so_luong_tong = $so_luong_tong;
    }

    public function getSoLuongHienTai()
    {
        return $this->so_luong_hien_tai;
    }

    public function setSoLuongHienTai($so_luong_hien_tai)
    {
        $this->so_luong_hien_tai = $so_luong_hien_tai;
    }

    public function getViTri()
    {
        return $this->vi_tri;
    }

    public function setViTri($vi_tri)
    {
        $this->vi_tri = $vi_tri;
    }

    public function getTenLoaiSach()
    {
        return $this->ten_loai_sach;
    }

    public function setTenLoaiSach($ten_loai_sach)
    {
        $this->ten_loai_sach = $ten_loai_sach;
    }

    public function getTenTG()
    {
        return $this->ten_tg;
    }

    public function setTenTG($ten_tg)
    {
        $this->ten_tg = $ten_tg;
    }

    
    public function toArray()
    {
        return [
            'ma_sach' => $this->ma_sach,
            'ten_sach' => $this->ten_sach,
            'ma_loai_sach' => $this->ma_loai_sach,
            'nha_xb' => $this->nha_xb,
            'nam_xb' => $this->nam_xb,
            'tinh_trang' => $this->tinh_trang,
            'mo_ta' => $this->mo_ta,
            'image' => $this->image,
            'ma_tg' => $this->ma_tg,
            'so_luong_tong' => $this->so_luong_tong,
            'so_luong_hien_tai' => $this->so_luong_hien_tai,
            'vi_tri' => $this->vi_tri,
            'ten_loai_sach' => $this->ten_loai_sach,
            'ten_tg' => $this->ten_tg,
        ];
    }
}
