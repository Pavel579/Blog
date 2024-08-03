<?php

namespace models\dto;

use DateTime;

class CommentDto
{
    public $id;
    public $comment;
    public int $authorId;
    public DateTime $dateCreated;
}