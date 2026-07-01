<?php
require_once __DIR__ . "/../../connect.php";
require_once __DIR__ . '/../../models/DocGia.php';

class DocGiaRepository
{
    private $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function getByUserId($user_id)
    {
        $sql = "SELECT * FROM docgia WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();

        $result = $stmt->get_result()->fetch_assoc();
        if ($result) {
            if (empty($result['ma_docgia'])) {
                $result['ma_docgia'] = "DG" . $user_id;
            }
            return new DocGia($result);
        }
        return null;
    }
}
