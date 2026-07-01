<?php
require_once __DIR__ . '/../../data/connect.php';
require_once __DIR__ . '/../../data/repositories/ThuThuRepository.php';
require_once __DIR__ . '/../../data/models/ThuThuModel.php';

class ThuThuService {
    private $repository;

    public function __construct() {
        $db = new Database();
        $conn = $db->getConnection();
        $this->repository = new ThuThuRepository($conn);
    }

    public function getAll() {
        return $this->repository->getAll();
    }

    public function getById($ma_thuthu) {
        return $this->repository->getById($ma_thuthu);
    }

    public function search($keyword) {
        return $this->repository->search($keyword);
    }

    public function add(ThuThuModel $thuthu) {
        return $this->repository->add($thuthu);
    }

    public function update(ThuThuModel $thuthu) {
        return $this->repository->update($thuthu);
    }

    public function delete($ma_thuthu) {
        return $this->repository->delete($ma_thuthu);
    }
}
