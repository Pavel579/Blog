<?php

namespace repository;

use PDO;

class CommentRepository
{
  private $conn;

  public function __construct($db) {
    $this->conn = $db;
  }

  public function createComment($authorName, $comment, $postId)
  {
    $query = "INSERT INTO comments (comment, post_id, author_id, date_created) VALUES (:comment, :postId, (select id from users where name = :authorName), now())";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':comment', $comment);
    $stmt->bindParam(':authorName', $authorName);
    $stmt->bindParam(':postId', $postId);
    try {
      return $stmt->execute();
    } catch (\PDOException $e) {
      var_dump($e);
      return false;
    }
  }

  public function getAllCommentsForPost(int $postId): array|false
  {
    $query = "SELECT c.*, u.name from comments c JOIN users u ON c.author_id = u.id WHERE post_id = :postId ORDER BY date_created DESC";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':postId', $postId);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}