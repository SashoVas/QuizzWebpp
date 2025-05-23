<?php
# database connection class
class Database {
    private $host = 'localhost';
    private $db = 'mysql_db';
    private $user = 'root';
    private $pass = '';
    public $pdo;

    public function __construct() {
        try {
            $this->pdo = new PDO(
                "mysql:host={$this->host};dbname={$this->db};charset=utf8mb4",
                $this->user,
                $this->pass
            );
        } catch (PDOException $e) {
            die("DB Error: " . $e->getMessage());
        }
    }
}

$db = new Database();
$pdo = $db->pdo; #this database object is used in the rest of the code
?>