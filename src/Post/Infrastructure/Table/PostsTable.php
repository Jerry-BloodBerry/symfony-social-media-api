<?php

namespace App\Post\Infrastructure\Table;

class PostsTable
{
  public const TABLE_NAME = 'posts';

  public const ID = 'uuid';
  public const AUTHOR_ID = 'author_id';
  public const CONTENT = 'content';
  public const CREATED_AT = 'created_at';
  public const UPDATED_AT = 'updated_at';
}
