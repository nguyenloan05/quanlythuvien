<?php

header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'];

try {
    
    if ($method === 'GET') {
        
        $dashboardData = [
            "thong_ke" => [
                "tong_sach" => 125,
                "tong_ban_sao" => 450,
                "so_khu_vuc" => 6,
                "so_tang" => 5
            ],
            "thong_bao_moi" => "Thư viện vừa nhập thêm 50 đầu sách Công nghệ thông tin mới!",
            "trang_thai" => "Đang mở cửa"
        ];

        echo json_encode($dashboardData, JSON_UNESCAPED_UNICODE);
    } 
    else {
        http_response_code(405);
        echo json_encode(["error" => "Method không hợp lệ. KhachAPI chỉ hỗ trợ GET."], JSON_UNESCAPED_UNICODE);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Lỗi Server: " . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}