<?php

namespace models;

class Blog
{
  public $id;
  public $text;
  public \DateTime $dateCreated;
  public User $author;

}