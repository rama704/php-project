<?php 
class Database
{
    private static $instance = null;

    private $conn;

    private $host = 'localhost';
    private $db = 'smart_store_db';
    private $user = 'root';
    private $pass = '';

    private $charset = 'utf8mb4';

    private function __construct()
    {
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->db . ';charset=' . $this->charset;

        $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->db);

        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
        $this->conn->set_charset($this->charset);
        
    }

    public static function getInstance()
    {
        if(self::$instance == null)
        {
            self::$instance = new Database();
        }
        return self::$instance;

    }
    public function getConnection()
    {
        return $this->conn;
    }



}
?>