<?php

require_once __DIR__ . '/../../../data/repositories/docgia/pd_DocGiaRepository.php';

class pd_DocGiaService
{
    private $repo;

    public function __construct()
    {
        $this->repo = new pd_DocGiaRepository();
    }

    public function getList($keyword, $user_id)
    {
        $ma_docgia = $this->repo->getMaDocGiaByUserId($user_id);

        $books = $this->repo->searchSach($keyword);

        foreach ($books as $book) {

            $book->trang_thai = $this->repo->getTrangThai(
                $ma_docgia,
                $book->ma_sach
            );
        }

        return [
            "success" => true,
            "data" => $books
        ];
    }

    public function datSach($ma_docgia, $ma_sach)
    {
        $cardStatus = $this->repo->getCardStatus($ma_docgia);
        if ($cardStatus === null) {
            return [
                "success" => false,
                "message" => "Bạn chưa đăng ký thẻ thư viện. Vui lòng đăng ký trước khi mượn sách!"
            ];
        }
        if ($cardStatus !== 1) {
            $msg = ($cardStatus === 0) ? "Thẻ của bạn đang chờ duyệt, vui lòng quay lại sau." : "Thẻ của bạn đã bị khóa.";
            return [
                "success" => false,
                "message" => $msg
            ];
        }

        $soLuong = $this->repo->checkSoLuong($ma_sach);

        if (!$soLuong || $soLuong['so_luong_hien_tai'] <= 0) {
            return [
                "success" => false,
                "message" => "Sách đã hết"
            ];
        }

        if ($this->repo->daDat($ma_docgia, $ma_sach)) {
            return [
                "success" => false,
                "message" => "Bạn đã đặt sách này rồi"
            ];
        }

        $insert = $this->repo->insertPhieu($ma_docgia, $ma_sach);

        if (!$insert) {
            return [
                "success" => false,
                "message" => "Đặt thất bại"
            ];
        }

        return [
            "success" => true,
            "message" => "Đặt thành công (chờ duyệt)"
        ];
    }

    public function huyDat($ma_pdt, $ma_docgia, $ma_sach)
    {
        if (!$ma_docgia || !$ma_sach) {
            return [
                "success" => false,
                "message" => "Thiếu dữ liệu"
            ];
        }

        $pdt = $this->repo->checkDaDat($ma_docgia, $ma_sach);

        if (!$pdt) {
            return [
                "success" => false,
                "message" => "Không tìm thấy phiếu đặt"
            ];
        }

        $ma_pdt_real = $pdt['ma_pdt'];

        if ((int)$pdt['trang_thai'] !== 0) {
            return [
                "success" => false,
                "message" => "Chỉ được hủy khi đang chờ duyệt"
            ];
        }

        $ok = $this->repo->updateTrangThai($ma_pdt_real, 3);

        if (!$ok) {
            return [
                "success" => false,
                "message" => "Hủy thất bại"
            ];
        }

        return [
            "success" => true,
            "message" => "Hủy đặt thành công"
        ];
    }

    public function getMaDocGia($user_id)
    {
        return $this->repo->getMaDocGiaByUserId($user_id);
    }
}
