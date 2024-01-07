<?php

namespace App\Tests\Controller;

use App\Domain\Interface\UserRepositoryInterface;
use App\Tests\BaseWebTestCase;
use App\Tests\Mocks\UserMocks;
use Ramsey\Uuid\Uuid;

class PostEndpointsTest extends BaseWebTestCase
{
  public function testCreatePost(): void
  {
    // given
    $client = static::createClient();
    self::bootKernel();
    $userRepository = static::getContainer()->get(UserRepositoryInterface::class);
    $uuid = Uuid::fromString('3c4f8061-3298-4904-9453-e84aab36ac12');
    $mockUser = (new UserMocks())
      ->withId($uuid)
      ->build();
    $userRepository->save($mockUser);

    // when
    $client->jsonRequest(method: 'POST', uri: '/api/post', parameters: [
      "content" => "This is my first post!",
      "authorId" => "3c4f8061-3298-4904-9453-e84aab36ac12"
    ]);

    // then
    $this->assertEquals(201, $client->getResponse()->getStatusCode());
    $this->assertJsonResponseHeaders($client->getResponse());
    $this->assertJson($client->getResponse()->getContent());
    // TODO: check verifying objects against JSON files
  }

  public function testPostDatabaseShouldBeEmpty(): void
  {
    // given
    $client = static::createClient();
    // when
    $client->request(method: 'GET', uri: '/api/post', server: ['ACCEPT' => 'application/json']);
    // then
    $this->assertEquals(200, $client->getResponse()->getStatusCode());
    $this->assertJsonResponseHeaders($client->getResponse());
    $content = $client->getResponse()->getContent();
    $this->assertJson($content);
    $posts = json_decode($content, true);
    $this->assertEmpty($posts['data']);
  }
}
