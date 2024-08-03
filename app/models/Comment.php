<?php

namespace models;

class Comment
{
  public $id;
  public $comment;
  public Post $post;
  public User $author;
  public \DateTime $dateCreated;
}