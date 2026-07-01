<?php
class Database
{
    private $host = "localhost";
    private $user = "root";
    private $pass = "";
    private $dbname = "demo_qltv";
    public $conn;

    public function getConnection()
    {
        $this->conn = mysqli_connect($this->host, $this->user, $this->pass, $this->dbname);

        if (!$this->conn) {
            die("Kết nối thất bại: " . mysqli_connect_error());
        }

        
        mysqli_set_charset($this->conn, "utf8mb4");

        return $this->conn;
    }
}
