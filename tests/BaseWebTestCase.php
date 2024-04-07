<?php

namespace App\Tests;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseWebTestCase extends WebTestCase
{
  protected static Connection $connection;

  protected function assertJsonResponseHeaders(Response $response): void
  {
    $this->assertTrue(
      $response->headers->contains(
        'Content-Type',
        'application/json'
      ),
      'the "Content-Type" header is "application/json"'
    );
  }

  protected function scrubUuids(string $json): string
  {
    return preg_replace('/[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}/', '<<scrubbed>>', $json);
  }
}
