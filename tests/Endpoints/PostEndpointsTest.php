<?php

namespace App\Tests\Endpoints;

use App\Common\ClockInterface;
use App\Post\Domain\PostRepositoryInterface;
use App\User\Domain\UserRepositoryInterface;
use App\Tests\BaseWebTestCase;
use App\Tests\Mocks\PostMocks;
use App\Tests\Mocks\UserMocks;
use Ramsey\Uuid\Uuid;

class PostEndpointsTest extends BaseWebTestCase
{
  private UserRepositoryInterface $userRepository;
  private PostRepositoryInterface $postRepository;
  private ClockInterface $clock;

  public function setUp(): void
  {
    parent::setUp();
    $this->userRepository = static::getContainer()->get(UserRepositoryInterface::class);
    $this->postRepository = static::getContainer()->get(PostRepositoryInterface::class);
    $this->clock = static::getContainer()->get(ClockInterface::class);
    self::ensureKernelShutdown();
  }
  private const POSTS_ENDPOINT_BASE_PATH = '/api/post';

  public function testShouldCreatePost(): void
  {
    // given
    $client = static::createClient();
    $uuidString = '3c4f8061-3298-4904-9453-e84aab36ac12';
    $uuid = Uuid::fromString($uuidString);
    $mockUser = (new UserMocks())
      ->withId($uuid)
      ->build();
    $this->userRepository->save($mockUser);
    // when
    $client->jsonRequest(method: 'POST', uri: self::POSTS_ENDPOINT_BASE_PATH, parameters: [
      'content' => 'This is my first post!',
      'authorId' => $uuidString
    ]);

    // then
    $this->assertEquals(201, $client->getResponse()->getStatusCode());
    $this->assertJsonResponseHeaders($client->getResponse());
    $content = $client->getResponse()->getContent();
    $this->assertJson($content);
    $scrubbedContent = $this->scrubUuids($content);
    $this->assertJsonStringEqualsJsonFile(__DIR__ . '/testShouldCreatePost.verified.json', $scrubbedContent);
  }

  public function testShouldRespondEmptyWhenNoPosts(): void
  {
    // given
    $client = static::createClient();
    // when
    $client->request(method: 'GET', uri: self::POSTS_ENDPOINT_BASE_PATH, server: ['ACCEPT' => 'application/json']);
    // then
    $this->assertEquals(200, $client->getResponse()->getStatusCode());
    $this->assertJsonResponseHeaders($client->getResponse());
    $content = $client->getResponse()->getContent();
    $this->assertJson($content);
    $posts = json_decode($content, true);
    $this->assertEmpty($posts['data']);
  }

  public function testShouldRespondPostWhenExists(): void
  {
    // given
    $client = static::createClient();
    $userUuid = Uuid::fromString('2415e8d9-85d9-4442-b517-c7d2712f6820');
    $mockUser = (new UserMocks())
      ->withId($userUuid)
      ->build();
    $this->userRepository->save($mockUser);
    $postStringUuid = 'b369fc40-6baf-4d4d-bdf8-b067ac3d6a03';
    $postUuid = Uuid::fromString($postStringUuid);
    $mockPost = (new PostMocks($this->clock))
      ->withId($postUuid)
      ->withAuthorId($userUuid)
      ->withCreatedAt(\DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s.v\Z', '2023-12-12T12:00:00.000Z'))
      ->build();
    $this->postRepository->save($mockPost);
    // when
    $client->request(
      method: 'GET',
      uri: self::POSTS_ENDPOINT_BASE_PATH . "/$postStringUuid",
      server: ['ACCEPT' => 'application/json']
    );
    // then
    $this->assertEquals(200, $client->getResponse()->getStatusCode());
    $this->assertJsonResponseHeaders($client->getResponse());
    $content = $client->getResponse()->getContent();
    $this->assertJson($content);
    $this->assertJsonStringEqualsJsonFile(__DIR__ . '/testShouldRespondPostWhenExists.verified.json', $content);
  }
}
