<?php

namespace repository;

use models\Comment;
use models\Post;
use models\User;
use PDO;

class CommentRepository
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    /**
     * Создание комментария
     * @param $authorName
     * @param $comment
     * @param $postId
     * @return array
     */
    public function createComment($authorName, $comment, $postId): array
    {
        $query = "INSERT INTO comments (comment, post_id, author_id, date_created) 
        VALUES (:comment, :postId, (select id from users where name = :authorName), now())";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':comment', $comment);
        $stmt->bindParam(':authorName', $authorName);
        $stmt->bindParam(':postId', $postId);
        try {
            return $stmt->execute();
        } catch (\PDOException $e) {
            return [];
        }
    }

    /**
     * Получаем дату последнего комментария пользователя в посте
     * @param $authorName
     * @param $postId
     * @return mixed
     */
    public function getLastUserCommentDate($authorName, $postId)
    {
        $query = "SELECT c.date_created FROM comments c JOIN users u on u.id = c.author_id WHERE c.post_id = :postId AND u.name = :authorName ORDER BY c.date_created DESC LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':postId', $postId);
        $stmt->bindParam(':authorName', $authorName);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    /**
     * Получаем список всех комментариев для поста
     * @param int $postId
     * @return array
     * @throws \Exception
     */
    public function getAllCommentsForPost(int $postId): array
    {
        $query = "SELECT c.*, u.name, p.text, p.date_created as post_date_created, p.author_id as post_author_id, 
        u2.name as post_author_name, u2.id as post_author_id from comments c JOIN users u ON c.author_id = u.id 
        JOIN posts p ON c.post_id = p.id JOIN users u2 on u2.id = p.author_id WHERE c.post_id = :postId 
        ORDER BY c.date_created DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':postId', $postId);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($result) > 0) {
            $comments = [];
            foreach ($result as &$row) {
                $comment = new Comment();
                $comment->id = $row['id'];
                $comment->comment = $row['comment'];
                $comment->dateCreated = new \DateTime($row['date_created']);
                $author = new User($row['author_id'], $row['name']);
                $comment->author = $author;
                $post = new Post();
                $post->id = $row['post_id'];
                $post->text = $row['text'];
                $post->dateCreated = new \DateTime($row['post_date_created']);
                $postAuthor = new User($row['post_author_id'], $row['post_author_name']);
                $post->author = $postAuthor;
                $comment->post = $post;
                $comments[] = $comment;
            }
            return $comments;
        }
        return [];
    }
}