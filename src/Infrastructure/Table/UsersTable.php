<?php

namespace App\Infrastructure\Table;

class UsersTable
{
  public const TABLE_NAME = 'users';

  public const ID = 'uuid';
  public const USERNAME = 'username';
  public const EMAIL = 'email';
  public const AVATAR_URL = 'avatar_url';
  public const CREATED_AT = 'created_at';
  public const UPDATED_AT = 'updated_at';
}
