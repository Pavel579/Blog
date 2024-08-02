<?php

namespace controllers;

use services\PostService;

class PostController
{
  private PostService $postService;

  function __construct($db)
  {
    $this->postService = new PostService($db);
  }

  /**
   * Получаем из БД блог по его id
   * @param int $id блога
   * @return void
   */
  public function getPostById(int $id): void
  {
    $post = $this->postService->getPostById($id);
    header('Content-type: application/json; charset=utf-8');
    echo json_encode($post);
  }

  /**
   * @throws \Exception
   */
  public function createPost(): void
  {
    $this->postService->createPost();
  }

  public function getListOfPosts($page = 1): void
  {
    $posts = $this->postService->getListOfPosts($page);
    header('Content-type: application/json; charset=utf-8');
    echo json_encode($posts);
  }
}