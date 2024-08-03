<?php

namespace controllers;

use services\CommentService;

class CommentController
{
    private CommentService $commentService;

    function __construct($commentService)
    {
        $this->commentService = $commentService;
    }

    /**
     * Создание комментария
     * @param int $postId
     * @return void
     * @throws \Exception
     */
    public function createComment(int $postId): void
    {
        $body = json_decode(file_get_contents('php://input'), true);
        $this->commentService->createComment($body, $postId);
    }

    /**
     * Получить все комментарии для поста
     * @param int $postId
     * @return void
     * @throws \Exception
     */
    public function getAllCommentsForPost(int $postId): void
    {
        $comments = $this->commentService->getAllCommentsForPost($postId);
        header('Content-type: application/json; charset=utf-8');
        echo json_encode($comments);
    }
}