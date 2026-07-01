<?php
class ThuThuModel {
    private $ma_thuthu;
    private $user_id;
    private $ho_ten;
    private $ngay_sinh;
    private $gioi_tinh;
    private $email;
    private $so_dien_thoai;
    private $dia_chi;
    private $ngay_vao_lam;
    private $chuc_vu;
    private $phong_ban;
    private $trang_thai;

    public function __construct($ma_thuthu = null, $user_id = null, $ho_ten = '', $ngay_sinh = null, $gioi_tinh = '', $email = '', $so_dien_thoai = '', $dia_chi = '', $ngay_vao_lam = null, $chuc_vu = '', $phong_ban = '', $trang_thai = 1) {
        $this->ma_thuthu = $ma_thuthu;
        $this->user_id = $user_id;
        $this->ho_ten = $ho_ten;
        $this->ngay_sinh = $ngay_sinh;
        $this->gioi_tinh = $gioi_tinh;
        $this->email = $email;
        $this->so_dien_thoai = $so_dien_thoai;
        $this->dia_chi = $dia_chi;
        $this->ngay_vao_lam = $ngay_vao_lam;
        $this->chuc_vu = $chuc_vu;
        $this->phong_ban = $phong_ban;
        $this->trang_thai = $trang_thai;
    }

    
    public function getMaThuThu() { return $this->ma_thuthu; }
    public function getUserId() { return $this->user_id; }
    public function getHoTen() { return $this->ho_ten; }
    public function getNgaySinh() { return $this->ngay_sinh; }
    public function getGioiTinh() { return $this->gioi_tinh; }
    public function getEmail() { return $this->email; }
    public function getSoDienThoai() { return $this->so_dien_thoai; }
    public function getDiaChi() { return $this->dia_chi; }
    public function getNgayVaoLam() { return $this->ngay_vao_lam; }
    public function getChucVu() { return $this->chuc_vu; }
    public function getPhongBan() { return $this->phong_ban; }
    public function getTrangThai() { return $this->trang_thai; }

    
    public function toArray() {
        return [
            "ma_thuthu" => $this->ma_thuthu,
            "user_id" => $this->user_id,
            "ho_ten" => $this->ho_ten,
            "ngay_sinh" => $this->ngay_sinh,
            "gioi_tinh" => $this->gioi_tinh,
            "email" => $this->email,
            "so_dien_thoai" => $this->so_dien_thoai,
            "dia_chi" => $this->dia_chi,
            "ngay_vao_lam" => $this->ngay_vao_lam,
            "chuc_vu" => $this->chuc_vu,
            "phong_ban" => $this->phong_ban,
            "trang_thai" => $this->trang_thai
        ];
    }
}
