<?php

namespace repository;

use models\Post;
use models\User;
use PDO;

class PostRepository
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    /**
     * Получить пост по его id
     * @param $id
     * @return Post
     * @throws \Exception
     */
    public function getPostById($id): Post
    {
        $query = "SELECT * FROM posts p JOIN users u on p.author_id = u.id WHERE p.id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (count($result) > 0) {
            return $this->getPost($result);
        } else {
            return new Post();
        }
    }

    /**
     * Создание поста
     * @param $authorName
     * @param $text
     * @return array
     */
    public function createPost($authorName, $text): array
    {
        $query = "INSERT INTO posts (text, author_id, date_created) VALUES (:text, (select id from users where name = :authorName), now())";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':text', $text);
        $stmt->bindParam(':authorName', $authorName);
        try {
            return $stmt->execute();
        } catch (\PDOException $e) {
            return [];
        }
    }

    /**
     * Получить списак всех постов
     * @param $limit
     * @param $offset
     * @return array
     * @throws \Exception
     */
    public function getAllPosts($limit, $offset): array
    {
        $query = "SELECT p.*, u.name FROM posts p JOIN users u ON p.author_id = u.id LIMIT :limit OFFSET :offset";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT );
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT );
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($result) > 0) {
            $posts = [];
            foreach ($result as &$row) {
                $post = $this->getPost($row);
                $posts[] = $post;
            }
            return $posts;
        }
        return [];
    }

    /**
     * Получить по 3 комментария каждого поста
     * @param $postIds
     * @return mixed
     */
    public function getThreeCommentsToEachPost($postIds): mixed
    {
        $postIdsString = implode(',', array_map('intval', $postIds));
        $query = "select t2.* from (select post_id from comments where post_id in ($postIdsString) group by post_id) as t1,
                 lateral (select * from comments where t1.post_id = comments.post_id order by date_created desc limit 3) as t2";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Маппинг поста
     * @param $result
     * @return Post
     * @throws \Exception
     */
    public function getPost($result): Post
    {
        $post = new Post();
        $post->id = $result['id'];
        $post->text = $result['text'];
        $post->dateCreated = new \DateTime($result['date_created']);
        $user = new User($result['author_id'], $result['name']);
        $post->author = $user;
        return $post;
    }
}