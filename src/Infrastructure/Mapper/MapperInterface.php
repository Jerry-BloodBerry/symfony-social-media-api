<?php

namespace App\Infrastructure\Mapper;

interface MapperInterface
{
  /**
   * @param array<string,mixed> $row
   */
  public function toEntity(array $row): object;

  /**
   * @return array<string,mixed>
   */
  public function toArray(object $entity): array;
}
