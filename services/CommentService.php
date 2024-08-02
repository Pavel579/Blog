<?php

namespace services;

use repository\CommentRepository;

class CommentService
{
  private Validator $validator;
  private CommentRepository $commentRepository;

  function __construct($db)
  {
    $this->commentRepository = new CommentRepository($db);
    $this->validator = new Validator();
  }

  /**
   * @throws \Exception
   */
  public function createComment(int $postId): void
  {
    $body = json_decode(file_get_contents('php://input'), true);
    $authorName = $body['author_name'];
    $comment = $body['comment'];
    $this->validator->validateText($comment, 200);
    if (!$this->commentRepository->createComment($authorName, $comment, $postId)) {
      throw new \Exception('Can\'t create comment');
    }
  }

  public function getAllCommentsForPost(int $postId): array|false
  {
    return $this->commentRepository->getAllCommentsForPost($postId);
  }
}