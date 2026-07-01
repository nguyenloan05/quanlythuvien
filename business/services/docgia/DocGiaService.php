<?php
require_once __DIR__ . "/../../../data/repositories/docgia/DocGiaRepository.php";
require_once __DIR__ . "/../thuthu/CardService.php";

class DocGiaService
{
    private $repo;
    private $cardService;

    public function __construct()
    {
        $this->repo = new DocGiaRepository();
        $this->cardService = new CardService();
    }

    public function kichHoatThe($data)
    {
        $ma_docgia = $data['ma_docgia'] ?? '';

        if (empty($ma_docgia)) {
            return ["status" => "error", "message" => "Thiếu mã độc giả."];
        }

        if (!$this->cardService->isNguoiDungHopLe($ma_docgia)) {
            return [
                "status" => "error",
                "message" => "Bạn đã có yêu cầu hoặc thẻ rồi."
            ];
        }

        $result = $this->cardService->requestNewCard($ma_docgia);

        return $result
            ? ["status" => "success", "message" => "Gửi yêu cầu thành công"]
            : ["status" => "error", "message" => "Lỗi khi tạo yêu cầu"];
    }

    public function lock($ma_docgia, $ly_do_khoa)
    {
        $card = $this->cardService->getByMaDG($ma_docgia);

        if (!$card || $card->trang_thai != 1) {
            return [
                "status" => "error",
                "message" => "Không tìm thấy thẻ"
            ];
        }

        $result = $this->cardService->lock($card->id, $ly_do_khoa);

        return [
            "status" => $result ? "success" : "error",
            "message" => $result ? "Đã khóa thẻ" : "Khóa thất bại"
        ];
    }

    public function getFullInfoByUserId($userId)
    {
        $reader = $this->repo->getByUserId($userId);
        if (!$reader) return null;

        $card = $this->cardService->getByMaDG($reader->ma_docgia);

        return [
            "reader" => $reader->toArray(),
            "card" => $card ? $card->toArray() : null
        ];
    }

    public function getReaderInfoByUserId($userId)
    {
        return $this->repo->getByUserId($userId);
    }
}
