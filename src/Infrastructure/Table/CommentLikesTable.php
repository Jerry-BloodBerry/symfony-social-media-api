<?php

namespace App\Infrastructure\Table;

class CommentLikesTable
{
  public const TABLE_NAME = 'likes';

  public const ID = 'id';
  public const AUTHOR_ID = 'author_id';
  public const COMMENT_ID = 'comment_id';
  public const CREATED_AT = 'created_at';
}
