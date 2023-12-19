<?php

namespace App\Infrastructure\Table;

class PostCommentsTable
{
  public const TABLE_NAME = 'post_comments';

  public const ID = 'id';
  public const AUTHOR_ID = 'author_id';
  public const POST_ID = 'post_id';
  public const CONTENT = 'content';
  public const CREATED_AT = 'created_at';
  public const UPDATED_AT = 'updated_at';
}
