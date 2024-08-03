<?php

namespace models;

class Post
{
  public $id;
  public $text;
  public \DateTime $dateCreated;
  public User $author;

}