<?php

namespace App\Tests;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Testcontainer\Container\PostgresContainer;


abstract class BaseWebTestCase extends WebTestCase
{

  /** @var PostgresContainer */
  protected static $postgresContainer;

  /** @var Connection */
  protected static $connection;

  public static function setUpBeforeClass(): void
  {
    $postgresVersion = $_ENV['POSTGRES_VERSION'];
    $postgresUser = $_ENV['POSTGRES_USER'];
    $postgresDatabase = $_ENV['POSTGRES_DATABASE'];
    $postgresDatabaseSuffix = $_ENV['POSTGRES_DATABASE_SUFFIX'];
    $postgresPassword = $_ENV['POSTGRES_PASSWORD'];

    // remember to pull the image before trying to run container if you have slow internet connection
    self::$postgresContainer = PostgresContainer::make($postgresVersion, $postgresPassword)
      ->withPostgresUser($postgresUser)
      ->withPostgresDatabase($postgresDatabase . $postgresDatabaseSuffix);
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
      '--no-interaction' // This flag is important to prevent any prompts during the test execution
    ]);

    $process->run();

    // Check if the process was successful
    if (!$process->isSuccessful()) {
      throw new ProcessFailedException($process);
    }
  }

  public static function tearDownAfterClass(): void
  {
    self::$postgresContainer->stop();
  }
}
