<?php
require_once __DIR__ . '/../../data/connect.php';
require_once __DIR__ . '/../../data/repositories/AdminRepository.php';
require_once __DIR__ . '/../../data/models/AdminModel.php';

class AdminService {
    private $repository;

    public function __construct() {
        $db = new Database();
        $conn = $db->getConnection();
        $this->repository = new AdminRepository($conn);
    }

    public function getDashboardData() {
        
        $rawData = $this->repository->getThongKeDashboard();
        
        
        $model = new AdminModel(
            $rawData['tong_doc_gia'],
            $rawData['the_hoat_dong'],
            $rawData['the_cho_duyet'],
            $rawData['the_bi_khoa'],
            $rawData['tong_phi_phat'],
            $rawData['top_sach']
        );

        
        return $model->toArray();
    }
}
?>