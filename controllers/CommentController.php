<?php

namespace controllers;

use services\CommentService;

class CommentController
{
  private CommentService $commentService;

  function __construct($db)
  {
    $this->commentService = new CommentService($db);
  }

  /**
   * @throws \Exception
   */
  public function createComment(int $postId): void
  {
    $this->commentService->createComment($postId);
  }

  public function getAllCommentsForPost(int $postId): void
  {
    $comments = $this->commentService->getAllCommentsForPost($postId);
    header('Content-type: application/json; charset=utf-8');
    echo json_encode($comments);
  }
}