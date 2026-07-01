<?php
session_start();
require_once 'data/connect.php';
$db = new Database();
$conn = $db->getConnection();

require_once 'business/middleware/AuthMiddleware.php';

$action = $_GET['action'] ?? 'khach';

switch ($action) {
    case 'khach':
        require_once 'business/services/SachService.php';
        $service = new SachService();
        $search = $_GET['search'] ?? '';
        $danhSachSach = $search ? $service->search($search) : $service->getAll();
        include 'presentation/views/KhachView/khachGUI.php';
        break;

    case 'chi_tiet':
        require_once 'business/services/SachService.php';
        $service = new SachService($conn);

        $id = $_GET['ma_sach'] ?? null;
        $book = $id ? $service->getById($id) : null;

        include 'presentation/views/KhachView/ChiTietSach.php';
        break;

    case 'khach_search':
        include 'presentation/views/KhachView/khach_search.php'; 
        break;

    case 'khach_qd':
        include 'presentation/views/KhachView/khach_qd.php'; 
        break;

    case 'khach_sukien':
        include 'presentation/views/KhachView/khach_sukien.php'; 
        break;
        
    case 'login':
        include 'presentation/views/login.php'; 
        break;

    case 'login_submit':
        
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        
        $sql = "SELECT u.*, dg.ma_docgia 
                FROM user u 
                LEFT JOIN docgia dg ON u.id = dg.user_id 
                WHERE u.username = ? AND u.password = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];
            $_SESSION['ma_dg'] = $row['ma_docgia']; 

            
            if ($row['role'] == 0) {
                
                header("Location: index.php?action=admin_dashboard");
                exit();
            } else if ($row['role'] == 1) {
                
                header("Location: index.php?action=trangchu_tt");
                exit();
            } else if ($row['role'] == 2) {
                
                header("Location: index.php?action=trangchu_sv");
                exit();
            } else {
                
                header("Location: index.php?action=khach");
                exit();
            }
        } else {
            
            $error = "Tên đăng nhập hoặc mật khẩu không chính xác!";
            include 'presentation/views/login.php';
        }
        break;

    case 'dangKy':
        
        include 'presentation/views/dangKy.php'; 
        break;

    case 'dangKy_submit':
        
        $username_input = $_POST['username'] ?? '';
        $password_input = $_POST['password'] ?? '';
        $ho_ten         = $_POST['ho_ten'] ?? '';
        $ngay_sinh      = $_POST['ngay_sinh'] ?? '';
        $gioi_tinh      = $_POST['gioi_tinh'] ?? '';
        $email          = $_POST['email'] ?? '';
        // $sdt            = $_POST['so_dien_thoai'] ?? '';
        $dia_chi        = $_POST['dia_chi'] ?? '';
        
        $role = 2; 

        
        $check_user = $conn->query("SELECT id FROM user WHERE username = '$username_input'");
        if ($check_user->num_rows > 0) {
            $error = "Tên đăng nhập đã tồn tại!";
            include 'presentation/views/dangKy.php';
            exit();
        }

        $conn->begin_transaction();
        try {
            
            $sql_user = "INSERT INTO user (username, password, role) VALUES ('$username_input', '$password_input', $role)";
            $conn->query($sql_user);
            $last_user_id = $conn->insert_id;

            
            $prefix = "DG" . date('y');
            do {
                $randomNumber = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
                $ma_dg_auto = $prefix . $randomNumber;
                $check = $conn->query("SELECT ma_docgia FROM docgia WHERE ma_docgia = '$ma_dg_auto'");
            } while ($check->num_rows > 0);

            
            $sql_profile = "INSERT INTO docgia (ma_docgia, user_id, ho_ten, ngay_sinh, gioi_tinh, email, dia_chi) 
                            VALUES ('$ma_dg_auto', $last_user_id, '$ho_ten', '$ngay_sinh', '$gioi_tinh', '$email', '$dia_chi')";
            $conn->query($sql_profile);

            
            

            $conn->commit();
            
            
            echo "<script>
                    alert('Đăng ký tài khoản thành công!\\nUsername: $username_input\\n\\nHãy đăng nhập để yêu cầu kích hoạt thẻ thư viện.');
                    window.location.href = 'index.php?action=login';
                  </script>";
            exit();
        } catch (Exception $e) {
            $conn->rollback();
            $error = "Lỗi đăng ký: " . $e->getMessage();
            include 'presentation/views/dangKy.php';
        }
        break;

    case 'trangchu_sv':
        
        AuthMiddleware::checkRole([2]);

        require_once 'business/services/DocGiaService.php';
        
        include 'presentation/views/DocGiaView/index_dg.php';
        break;

    case 'trangchu_tt':
        
        AuthMiddleware::checkRole([1]);
        
        include 'presentation/views/ThuThuView/index_tt.php';
        break;

    case 'admin_dashboard':
        
        AuthMiddleware::checkRole([0]);
        
        include 'presentation/views/AdminView/index_admin.php';
        break;

    case 'logout':
        session_unset();
        session_destroy();
        header("Location: index.php?action=khach");
        exit();
        break;

    default:
        include 'presentation/views/KhachView/khachGUI.php';
        break;
}