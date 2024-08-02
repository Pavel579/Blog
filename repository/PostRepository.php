<?php

namespace repository;

use PDO;

class PostRepository
{
  private $conn;

  public function __construct($db) {
    $this->conn = $db;
  }

  public function getPostById($id): array|false {
    $query = "SELECT * FROM posts WHERE id = :id";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public function createPost($authorName, $text)
  {
    $query = "INSERT INTO posts (text, author_id, date_created) VALUES (:text, (select id from users where name = :authorName), now())";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':text', $text);
    $stmt->bindParam(':authorName', $authorName);
    try{
      return $stmt->execute();
    } catch (\PDOException $e) {
      return false;
    }
  }

  public function getAllPosts()
  {
    $query = "SELECT * FROM posts";
    $stmt = $this->conn->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function getThreeCommentsToEachPost()
  {
    $query = "select t2.* from (select post_id from comments group by post_id) as t1,
                 lateral (select * from comments where t1.post_id = comments.post_id order by date_created desc limit 3) as t2";
    $stmt = $this->conn->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function getListOfPosts($limit, $offset)
  {
    $query="";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
    $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}