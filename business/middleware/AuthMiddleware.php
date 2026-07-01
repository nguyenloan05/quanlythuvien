<?php
class AuthMiddleware {
    
    public static function checkLogin() {
        
        if (!isset($_SESSION['user_id'])) {
            header("Location: /hocphp/pttkpm_QLTV moi/index.php?action=login");
            exit();
        }
    }

    public static function checkRole($allowedRoles) {
        self::checkLogin();
        
        $userRole = isset($_SESSION['role']) ? $_SESSION['role'] : null; 

        if (!in_array($userRole, $allowedRoles)) {
            
            header("Location: /hocphp/pttkpm_QLTV moi/index.php?action=khach");
            exit();
        }
    }
}
?>