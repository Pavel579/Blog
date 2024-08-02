<?php

namespace services;

use repository\PostRepository;

class PostService
{
  private PostRepository $postRepository;
  private Validator $validator;
  function __construct($db)
  {
    $this->postRepository = new PostRepository($db);
    $this->validator = new Validator();
  }

  public function getPostById(int $id): array|false
  {
    return $this->postRepository->getPostById($id);
  }

  /**
   * @throws \Exception
   */
  public function createPost(): void
  {
    $body = json_decode(file_get_contents('php://input'), true);
    var_dump($body['author_name']);
    $authorName = $body['author_name'];
    $text = $body['text'];
    $this->validator->validateText($text, 5000);
    if (!$this->postRepository->createPost($authorName, $text)) {
      throw new \Exception('Can\'t create post');
    }
  }

  public function getListOfPosts($page)
  {
    $limit = 15; // количество записей на странице
    $offset = ($page - 1) * $limit; // смещение для LIMIT в запросе
    $posts = $this->postRepository->getAllPosts();
    $comments = $this->postRepository->getThreeCommentsToEachPost();
    foreach ($posts as &$post) {
      $post['comments'] = array_filter($comments, function ($comment) use ($post) {
        return $comment['post_id'] === $post['id'];
      });
    }
    return $posts;
  }
}