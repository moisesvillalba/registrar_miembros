<?php
// config/database.php - Versión compatible con todas las versiones de PHP

class Database
{
    // Configuración para PRODUCCIÓN (Byethost)
    public $host;
    public $db_name;
    public $username;
    public $password;
    public $conn;
    public $connection_error;

    public function __construct()
    {
        $this->host = "sql111.byethost3.com";
        $this->db_name = "b3_38781053_sistema_registro";
        $this->username = "b3_38781053";
        $this->password = "s8Fg\$mHNh#CL#kn";
        $this->conn = null;
        $this->connection_error = null;
    }

    public function getConnection()
    {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password,
                array(
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
                )
            );
        } catch (PDOException $exception) {
            $this->connection_error = $exception->getMessage();
            return null;
        }

        return $this->conn;
    }
}
