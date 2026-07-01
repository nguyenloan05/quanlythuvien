<?php

require_once __DIR__ . '/../../data/connect.php';
require_once __DIR__ . '/../../data/repositories/DocGiaRepository.php';
require_once __DIR__ . '/../../data/models/DocGiaModel.php';

class DocGiaService {
    private $repository;

    public function __construct() {
        $db = new Database();
        $conn = $db->getConnection();
        $this->repository = new DocGiaRepository($conn);
    }

    public function getAll() {
        return $this->repository->getAll();
    }

    public function getById($ma_docgia) {
        return $this->repository->getById($ma_docgia);
    }

    public function search($keyword) {
        return $this->repository->search($keyword);
    }

    public function add(DocGiaModel $docgia, $files = null) {
        
        $anh = $this->handleUploadImage($files);
        if ($anh !== '') {
            $docgia->setAnhChanDung($anh); 
        }
        return $this->repository->add($docgia);
    }

    public function update(DocGiaModel $docgia, $files = null) {
        
        if ($files != null && isset($files['file_anh']) && $files['file_anh']['error'] == 0) {
            $anh = $this->handleUploadImage($files);
            $docgia->setAnhChanDung($anh);
        } else {
            
            $current = $this->repository->getById($docgia->getMaDocGia());
            if ($current && $current->getAnhChanDung()) {
                $docgia->setAnhChanDung($current->getAnhChanDung());
            } else {
                $docgia->setAnhChanDung('default_avatar.png');
            }
        }
        return $this->repository->update($docgia);
    }

    public function delete($ma_docgia) {
        return $this->repository->delete($ma_docgia);
    }

    public function checkTrung($username, $ma_dg) {
        return $this->repository->checkTrung($username, $ma_dg);
    }

    public function createWithAccount(DocGiaModel $docgia, $username, $password) {
        return $this->repository->createWithAccount($docgia, $username, $password);
    }

    public function updatePassword($userId, $password) {
        return $this->repository->updatePassword($userId, $password);
    }

    
    private function handleUploadImage($files) {
        if ($files && isset($files['file_anh']) && $files['file_anh']['error'] == 0) {
            $fileName = time() . '_' . basename($files['file_anh']['name']);
            
            $targetDir = __DIR__ . '/../../presentation/assets/images/'; 
            $targetFilePath = $targetDir . $fileName;

            if (move_uploaded_file($files['file_anh']['tmp_name'], $targetFilePath)) {
                return $fileName; 
            }
        }
        return '';
    }
}