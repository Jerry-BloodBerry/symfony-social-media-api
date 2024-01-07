<?php

namespace App\Tests;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Testcontainer\Container\PostgresContainer;


abstract class BaseWebTestCase extends WebTestCase
{
  protected static PostgresContainer $postgresContainer;
  protected static Connection $connection;

  public static function setUpBeforeClass(): void
  {
    $postgresVersion = $_ENV['POSTGRES_VERSION'];
    $postgresUser = $_ENV['POSTGRES_USER'];
    $postgresDatabase = $_ENV['POSTGRES_DATABASE'];
    $postgresDatabaseSuffix = $_ENV['POSTGRES_DATABASE_SUFFIX'];
    $postgresPassword = $_ENV['POSTGRES_PASSWORD'];

    self::$postgresContainer = PostgresContainer::make($postgresVersion, $postgresPassword)
      ->withPostgresUser($postgresUser)
      ->withPostgresDatabase($postgresDatabase . $postgresDatabaseSuffix);
    // remember to pull the image before trying to run container if you have slow internet connection
    self::$postgresContainer->run();

    self::updateDatabaseUrl(
      $postgresUser,
      $postgresPassword,
      $postgresDatabase . $postgresDatabaseSuffix,
      $postgresVersion
    );

    self::runDoctrineMigrations();

    self::updateDatabaseUrl($postgresUser, $postgresPassword, $postgresDatabase, $postgresVersion);
  }

  private static function updateDatabaseUrl(
    string $postgresUser,
    string $postgresPassword,
    string $postgresDatabase,
    string $postgresVersion
  ): void {
    $_ENV['DATABASE_URL'] = sprintf(
      'postgresql://%s:%s@%s:%d/%s?serverVersion=%s&charset=utf8',
      $postgresUser,
      $postgresPassword,
      self::$postgresContainer->getAddress(),
      5432,
      $postgresDatabase,
      $postgresVersion
    );
  }

  private static function runDoctrineMigrations(): void
  {
    $process = new Process([
      'php',
      'bin/console',
      'doctrine:migrations:migrate',
      '--no-interaction',
    ]);

    $process->run();

    if (!$process->isSuccessful()) {
      throw new ProcessFailedException($process);
    }
  }

  public static function tearDownAfterClass(): void
  {
    self::$postgresContainer->stop();
  }

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
}
