<?php
require_once __DIR__ . '/../../data/repositories/TacGiaRepository.php';

class TacGiaService
{

    private $repo;

    public function __construct()
    {
        $this->repo = new TacGiaRepository();
    }

    public function getAll()
    {
        return $this->repo->getAll();
    }

    public function getById($id)
    {
        if (empty($id)) throw new Exception("ID không hợp lệ");
        return $this->repo->getById($id);
    }

    public function add($tg, $files = null)
    {
        if (empty($tg->getTenTG()))
            throw new Exception("Tên không được rỗng");

        if ($files && isset($files['hinh'])) {
            $tg->setHinh($this->uploadHinh($files['hinh']));
        }

        return $this->repo->insert($tg);
    }

    public function update($tg, $files = null)
    {
        if (empty($tg->getMaTG()))
            throw new Exception("ID không hợp lệ");

        if ($files && $files['hinh']['name']) {
            $tg->setHinh($this->uploadHinh($files['hinh']));
        }

        return $this->repo->update($tg);
    }

    public function delete($id)
    {
        if (empty($id)) {
            throw new Exception("ID không hợp lệ");
        }

        
        if ($this->repo->hasBooks($id)) {
            throw new Exception("Không thể xoá  vì tác giả đã có sách!");
        }

        return $this->repo->delete($id);
    }

    private function uploadHinh($file)
    {
        
        $dir = __DIR__ . '/../../presentation/assets/images/';
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = 'tg_' . time() . '.' . $ext;

        
        move_uploaded_file($file['tmp_name'], $dir . $fileName);

        
        return $fileName;
    }
}
