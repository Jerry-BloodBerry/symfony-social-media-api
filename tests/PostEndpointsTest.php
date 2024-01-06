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
    $client->request(method: 'POST', uri: '/api/post', content: json_encode([
      "content" => "This is my first post!",
      "authorId" => "3c4f8061-3298-4904-9453-e84aab36ac12"
    ]), server: ['CONTENT_TYPE' => 'application/json']);

    // then
    $this->assertEquals(201, $client->getResponse()->getStatusCode());

    // Check if the content type is JSON
    $this->assertTrue(
      $client->getResponse()->headers->contains(
        'Content-Type',
        'application/json'
      ),
      'the "Content-Type" header is "application/json"'
    );

    // Check if the response content is JSON
    $this->assertJson($client->getResponse()->getContent());

  }
}
