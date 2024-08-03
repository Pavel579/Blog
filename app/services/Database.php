<?php

namespace services;

use PDO;
use PDOException;

class Database
{
    public $conn;

    public function getConnection(): ?PDO
    {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host={$_ENV["DB_HOST"]};dbname={$_ENV["DB_NAME"]}", $_ENV["DB_USER"], $_ENV["DB_PASSWORD"]);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        return $this->conn;
    }
}