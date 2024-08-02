<?php

namespace config;

use PDO;
use PDOException;

class Database
{
  private string $host = 'blog_mysql-db_1';
  private string $db_name = 'blog_db'; // Замените на имя вашей базы данных
  private string $username = 'db_user'; // Замените на ваше имя пользователя
  private string $password = 'password'; // Замените на ваш пароль
  public $conn;

  public function getConnection(): ?PDO
  {
    $this->conn = null;
    try {
      $this->conn = new PDO("mysql:host={$this->host};dbname={$this->db_name}", $this->username, $this->password);
      $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $exception) {
      echo "Connection error: " . $exception->getMessage();
    }
    return $this->conn;
  }
}