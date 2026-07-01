<?php
require_once __DIR__ . "/../../../data/repositories/thuthu/PhieudatRepository.php";

class PhieuDatService
{
    private $repo;

    public function __construct()
    {
        $this->repo = new PhieuDatRepository();
    }

    public function getAll()
    {
        $items = $this->repo->getAll();
        return array_map(function ($item) {
            return $item->toArray();
        }, $items);
    }

    public function approve($id)
    {
        $data = $this->repo->getById($id);

        if (!$data || $data['trang_thai'] != 0) return false;
        if ($data['so_luong_hien_tai'] <= 0) return false;
        $result =  $this->repo->updateStatusApprove(
            $id,
            date('Y-m-d', strtotime('+15 days'))
        );
        if ($result) {
            $this->repo->decreaseStock($data['ma_sach']);
        }
        return $result;
    }

    public function reject($id)
    {
        return $this->repo->reject($id);
    }

    public function markReceived($id)
    {
        $data = $this->repo->getById($id);

        if (!$data || $data['trang_thai'] != 1) return false;

        // ❗ check kho
        if ($data['so_luong_hien_tai'] <= 0) return false;

        // set hạn trả + trạng thái
        $this->repo->updateReceived(
            $id,
            date('Y-m-d', strtotime('+15 days'))
        );

        return true;
    }
}
