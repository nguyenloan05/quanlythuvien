<?php
require_once __DIR__ . '/../../data/repositories/SachRepository.php';

class SachService
{
    private $repo;

    public function __construct()
    {
        $this->repo = new SachRepository();
    }

    public function getAll()
    {
        return $this->repo->getAll();
    }

    public function getById($id)
    {
        if (empty($id)) {
            throw new Exception("ID không hợp lệ");
        }

        return $this->repo->getById($id);
    }

    public function add($sach, $files = null)
    {
        if ($files && isset($files['image']) && $files['image']['name']) {
            $imagePath = $this->uploadHinh($files['image']);
            $sach->setImage($imagePath);
        } else {
            $sach->setImage($sach->getImage() ?? "");
        }

        $soLuongThem = $sach->getSoLuongTong();

        if ($soLuongThem === null || $soLuongThem <= 0) {
            throw new Exception("Số lượng không hợp lệ");
        }
        if ($sach->getSoLuongHienTai() === null) {
            $sach->setSoLuongHienTai($soLuongThem);
        }

        $existing = $this->repo->getByTenSach($sach->getTenSach());
        if ($existing) {

            $existing->setSoLuongTong(
                $existing->getSoLuongTong() + $soLuongThem
            );

            $existing->setSoLuongHienTai(
                $existing->getSoLuongHienTai() + $soLuongThem
            );

            $result = $this->repo->update($existing);

            if (!$result) {
                throw new Exception("Cập nhật số lượng sách thất bại");
            }

            return $result;
        }

        
        $result = $this->repo->insert($sach);

        if (!$result) {
            throw new Exception("Thêm sách thất bại (lỗi DB)");
        }

        return $result;
    }

    public function update($sach, $files = null)
    {
        if (empty($sach->getMaSach())) {
            throw new Exception("ID không hợp lệ");
        }

        if ($files && isset($files['image']) && $files['image']['name']) {
            $imagePath = $this->uploadHinh($files['image']);
            $sach->setImage($imagePath);
        } else {
            $sach->setImage($sach->getImage() ?? "");
        }

        $result = $this->repo->update($sach);

        if (!$result) {
            throw new Exception("Cập nhật thất bại");
        }

        return $result;
    }

    public function delete($id)
    {
        if (empty($id)) {
            throw new Exception("ID không hợp lệ");
        }

        $sach = $this->repo->getById($id);
        if (!$sach) {
            throw new Exception("Sách không tồn tại");
        }

        if ($this->repo->isSachTrongPhieuMuon($id)) {
            throw new Exception("Sách đang có phiếu mượn, không thể xoá");
        }

        $result = $this->repo->delete($id);

        if (!$result) {
            throw new Exception("Xoá thất bại");
        }

        return true;
    }

    public function search($keyword)
    {
        return $this->repo->search($keyword);
    }

    private function uploadHinh($file)
    {
        $dir = __DIR__ . '/../../presentation/assets/images/';
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = 's_' . time() . '.' . $ext;
        move_uploaded_file($file['tmp_name'], $dir . $fileName);
        return $fileName;
    }
}
