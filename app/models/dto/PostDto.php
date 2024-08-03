<?php

namespace models\dto;

use models\User;

class PostDto
{
    public $id;
    public $text;
    public \DateTime $dateCreated;
    public User $author;
    /**
     * @var CommentDto[]
     */
    public array $comments;
}