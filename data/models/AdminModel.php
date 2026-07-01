<?php
class AdminModel {
    private $tong_doc_gia;
    private $the_hoat_dong;
    private $the_cho_duyet;
    private $the_bi_khoa;
    private $tong_phi_phat;
    private $top_sach; 

    public function __construct($tong_doc_gia, $the_hoat_dong, $the_cho_duyet, $the_bi_khoa, $tong_phi_phat, $top_sach) {
        $this->tong_doc_gia = $tong_doc_gia;
        $this->the_hoat_dong = $the_hoat_dong;
        $this->the_cho_duyet = $the_cho_duyet;
        $this->the_bi_khoa = $the_bi_khoa;
        $this->tong_phi_phat = $tong_phi_phat;
        $this->top_sach = $top_sach;
    }

    
    public function toArray() {
        return [
            'tong_doc_gia'  => $this->tong_doc_gia,
            'the_hoat_dong' => $this->the_hoat_dong,
            'the_cho_duyet' => $this->the_cho_duyet,
            'the_bi_khoa'   => $this->the_bi_khoa,
            'tong_phi_phat' => $this->tong_phi_phat,
            'top_sach'      => $this->top_sach
        ];
    }
}
?>