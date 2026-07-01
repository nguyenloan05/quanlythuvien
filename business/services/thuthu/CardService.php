<?php
require_once __DIR__ . '/../../../data/repositories/thuthu/CardRepository.php';

class CardService
{
    private $repo;

    public function __construct()
    {
        $this->repo = new CardRepository();
    }

    public function randomMaThe()
    {
        do {
            $newCode = "LIB" . str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
            $isExist = $this->repo->checkCodeExists($newCode);
        } while ($isExist);

        return $newCode;
    }

    public function activateAuto($id)
    {
        if (!$id || !is_numeric($id)) {
            return false;
        }

        $card = $this->repo->getById($id);

        if (!$card || $card->trang_thai != 0) {
            return false;
        }
        $ma_the_moi = $this->randomMaThe();
        return $this->repo->activate($id, $ma_the_moi);
    }

    public function requestNewCard($ma_docgia)
    {
        if (!$this->isNguoiDungHopLe($ma_docgia)) {
            return false;
        }

        return $this->repo->createRegistration($ma_docgia);
    }

    public function isNguoiDungHopLe($ma_docgia)
    {
        $card = $this->repo->getByMaDG($ma_docgia);

        if (!$card) return true;

        switch ($card->trang_thai) {
            case 0: // chờ duyệt
            case 1: // đang hoạt động
                return false;

            case 2: // đã khóa → cho đăng ký lại
                return true;

            default:
                return false;
        }
    }

    public function getAll()
    {
        $cards = $this->repo->getAll();

        return array_map(function ($card) {
            return $card->toArray();
        }, $cards);
    }

    public function getByMaDG($ma_docgia)
    {
        return $this->repo->getByMaDG($ma_docgia);
    }

    public function lock($id, $ly_do_khoa)
    {
        $card = $this->repo->getById($id);

        if (!$card || $card->trang_thai != 1) {
            return false; // chỉ khóa thẻ đang hoạt động
        }

        return $this->repo->lock($id, $ly_do_khoa);
    }

    public function unlock($id)
    {
        $card = $this->repo->getById($id);

        if (!$card || $card->trang_thai != 2) {
            return false;
        }

        return $this->repo->unlock($id);
    }

    public function getById($id)
    {
        $card = $this->repo->getById($id);

        return $card ? $card->toArray() : null;
    }
}
