<?php

namespace controllers;

use services\PostService;

class PostController
{
    private PostService $postService;

    function __construct($postService)
    {
        $this->postService = $postService;
    }

    /**
     * Получаем из БД блог по его id
     * @param int $id блога
     * @return void
     * @throws \Exception
     */
    public function getPostById(int $id): void
    {
        $post = $this->postService->getPostById($id);
        header('Content-type: application/json; charset=utf-8');
        echo json_encode($post);
    }

    /**
     * Создание поста
     * @throws \Exception
     */
    public function createPost(): void
    {
        $body = json_decode(file_get_contents('php://input'), true);
        $this->postService->createPost($body);
    }

    /**
     * Получение списка постов
     * @param int $page
     * @return void
     * @throws \Exception
     */
    public function getListOfPostsWithThreeComments(int $page = 1): void
    {
        $posts = $this->postService->getListOfPostsThreeComments($page);
        header('Content-type: application/json; charset=utf-8');
        echo json_encode($posts);
    }

}