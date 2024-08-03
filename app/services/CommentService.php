<?php

namespace services;

use DateTime;
use repository\CommentRepository;

class CommentService
{
    private Validator $validator;
    private CommentRepository $commentRepository;

    function __construct($commentRepository, $validator)
    {
        $this->commentRepository = $commentRepository;
        $this->validator = $validator;
    }

    /**
     * Создание комментария
     * @param $body
     * @param int $postId
     * @return void
     * @throws \Exception
     */
    public function createComment($body, int $postId): void
    {
        $authorName = $body['author_name'];
        $comment = $body['comment'];
        $this->validator->validateText($comment, 200);
        $resultDate = $this->commentRepository->getLastUserCommentDate($authorName, $postId);
        $now = new DateTime();
        $now->format('Y-m-d H:i:s');
        $lastUserCommentDate = new DateTime($resultDate['date_created']);
        $interval = $now->diff($lastUserCommentDate);
        $minutesDifference = $interval->i;
        if ($minutesDifference >= 1) {
            throw new \Exception('You can post comment only 1 time in minute');
        }
        if (!$this->commentRepository->createComment($authorName, $comment, $postId)) {
            throw new \Exception('Can\'t create comment');
        }
    }

    /**
     * Получить все комментарии для поста
     * @param int $postId
     * @return array
     * @throws \Exception
     */
    public function getAllCommentsForPost(int $postId): array
    {
        return $this->commentRepository->getAllCommentsForPost($postId);
    }
}