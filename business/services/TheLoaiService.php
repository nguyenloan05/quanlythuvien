<?php
require_once __DIR__ . '/../../data/repositories/TheLoaiRepository.php';

class TheLoaiService
{
    private TheLoaiRepository $repo;

    public function __construct()
    {
        $this->repo = new TheLoaiRepository();
    }

    public function getAll(): array
    {
        return $this->repo->getAll();
    }

    public function getById(string $id)
    {
        $this->validateId($id);
        return $this->repo->getById($id);
    }

    public function add(TheLoaiModel $tl): bool
    {
        $this->validateTheLoai($tl);

        
        if ($this->repo->getById($tl->getTenLoaiSach())) {
            throw new Exception("Tên thể loại đã tồn tại");
        }

        return $this->repo->insert($tl);
    }

    public function update(TheLoaiModel $tl): bool
    {
        $this->validateId($tl->getMaLoaiSach());

        if (!$this->repo->getById($tl->getMaLoaiSach())) {
            throw new Exception("Thể loại không tồn tại");
        }

        if (empty($tl->getTenLoaiSach())) {
            throw new Exception("Tên thể loại không được rỗng");
        }

        return $this->repo->update($tl);
    }

    public function delete(string $id): bool
    {
        $this->validateId($id);
        if (!$this->repo->getById($id)) {
            throw new Exception("Thể loại không tồn tại");
        }
        if ($this->repo->hasBooks($id)) {
            throw new Exception("Thể loại này đang có sách, không thể xóa");
        }

        return $this->repo->delete($id);
    }

    private function validateId(?string $id): void
    {
        if (empty($id)) {
            throw new Exception("Mã thể loại không hợp lệ");
        }
    }

    private function validateTheLoai(TheLoaiModel $tl): void
    {
        if (empty($tl->getTenLoaiSach())) {
            throw new Exception("Tên thể loại không được rỗng");
        }
    }
}
