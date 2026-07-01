<?php

class Card
{
    public $id;
    public $ma_the;
    public $ma_docgia;
    public $ngay_cap;
    public $ngay_het_han;
    public $trang_thai;
    public $ly_do_khoa;

    public $ho_ten;
    public $email;
    public $anh_chan_dung;

    public function __construct($data = [])
    {
        $this->id = $data['id'] ?? null;
        $this->ma_the = $data['ma_the'] ?? null;
        $this->ma_docgia = $data['ma_docgia'] ?? null;
        $this->ngay_cap = $data['ngay_cap'] ?? null;
        $this->ngay_het_han = $data['ngay_het_han'] ?? null;
        $this->trang_thai = (int)($data['trang_thai'] ?? 0);
        $this->ly_do_khoa = $data['ly_do_khoa'] ?? null;

        $this->ho_ten = $data['ho_ten'] ?? null;
        $this->email = $data['email'] ?? null;
        $this->anh_chan_dung = $data['anh_chan_dung'] ?? 'default_avatar.png';
    }

    public function getStatusText()
    {
        switch ($this->trang_thai) {
            case 0:
                return "Chờ phê duyệt";
            case 1:
                return "Đang hoạt động";
            case 2:
                return "Đã khóa";
            default:
                return "Không xác định";
        }
    }

    public function toArray()
    {
        return [
            "id" => $this->id,
            "ma_the" => $this->ma_the,
            "ma_docgia" => $this->ma_docgia,
            "ngay_cap" => $this->ngay_cap,
            "ngay_het_han" => $this->ngay_het_han,
            "trang_thai" => $this->trang_thai,
            "status_text" => $this->getStatusText(),
            "ly_do_khoa" => $this->ly_do_khoa,
            "ho_ten" => $this->ho_ten,
            "email" => $this->email,
            "anh_chan_dung" => $this->anh_chan_dung
        ];
    }
}
