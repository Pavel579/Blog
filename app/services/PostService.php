<?php

namespace services;

use models\dto\CommentDto;
use models\dto\PostDto;
use models\Post;
use repository\PostRepository;

class PostService
{
    private PostRepository $postRepository;
    private Validator $validator;

    function __construct($postRepository, $validator)
    {
        $this->postRepository = $postRepository;
        $this->validator = $validator;
    }

    /**
     * Получение поста по его id
     * @param int $id
     * @return Post
     * @throws \Exception
     */
    public function getPostById(int $id): Post
    {
        return $this->postRepository->getPostById($id);
    }

    /**
     * Создание поста
     * @param $body
     * @return void
     * @throws \Exception
     */
    public function createPost($body): void
    {
        $authorName = $body['author_name'];
        $text = $body['text'];
        $this->validator->validateText($text, 5000);
        if (!$this->postRepository->createPost($authorName, $text)) {
            throw new \Exception('Can\'t create post');
        }
    }

    /**
     * Получить список постов c 3 комментариями в каждом
     * @param $page
     * @return array
     * @throws \Exception
     */
    public function getListOfPostsThreeComments($page): array
    {
        $limit = 15; // количество записей на странице
        $offset = ($page - 1) * $limit; // смещение для LIMIT в запросе
        $posts = $this->postRepository->getAllPosts($limit, $offset);
        if (empty($posts)) {
            return [];
        }
        $postIds = [];
        $postDtos = [];
        foreach ($posts as $post) {
            $postIds[] = $post->id;

            $postDto = new PostDto();
            $postDto->id = $post->id;
            $postDto->text = $post->text;
            $postDto->dateCreated = $post->dateCreated;
            $postDto->author = $post->author;

            $postDtos[] = $postDto;
        }

        $comments = $this->postRepository->getThreeCommentsToEachPost($postIds);

        foreach ($comments as $commentData) {
            $commentDto = new CommentDto();
            $commentDto->id = $commentData['id'];
            $commentDto->comment = $commentData['comment'];
            $commentDto->authorId = $commentData['author_id'];
            $commentDto->dateCreated = $commentData['date_created'];

            foreach ($postDtos as $postDto) {
                if ($postDto->id === $commentData['post_id']) {
                    $postDto->comments[] = $commentDto;
                    break;
                }
            }
        }
        return $postDtos;
    }
}