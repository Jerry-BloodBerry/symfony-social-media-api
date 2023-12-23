<?php

namespace App\Controller;

use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\Serializer\JsonApiSerializer;
use League\Fractal\TransformerAbstract;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseApiController extends AbstractController
{
  private Manager $fractal;

  public function __construct()
  {
    $this->fractal = new Manager();
    $this->fractal->setSerializer(new JsonApiSerializer());
  }

  /**
   * @param array<string,string> $headers
   */
  protected function respondWithItem(
    mixed $data,
    callable|TransformerAbstract|null $transformer,
    string $resourceKey,
    int $status = Response::HTTP_OK,
    array $headers = []
  ): JsonResponse {
    $resource = new Item($data, $transformer, $resourceKey);
    $rootScope = $this->fractal->createData($resource);

    return new JsonResponse($rootScope->toArray(), $status, $headers);
  }

  /**
   * @param array<string,string> $headers
   */
  protected function respondWithCollection(
    mixed $data,
    callable|TransformerAbstract|null $transformer,
    string $resourceKey,
    int $status = Response::HTTP_OK,
    array $headers = []
  ): JsonResponse {
    $resource = new Collection($data, $transformer, $resourceKey);
    $rootScope = $this->fractal->createData($resource);

    return new JsonResponse($rootScope->toArray(), $status, $headers);
  }

  /**
   * @param array<string,string> $headers
   */
  protected function respondWithCreated(
    mixed $data,
    callable|TransformerAbstract|null $transformer,
    string $resourceKey,
    array $headers = []
  ): JsonResponse {
    $resource = new Item($data, $transformer, $resourceKey);
    $rootScope = $this->fractal->createData($resource);

    return new JsonResponse($rootScope->toArray(), Response::HTTP_CREATED, $headers);
  }

  protected function respondWithNotFound(string $message = 'Not Found'): JsonResponse
  {
    return $this->respondWithError('Not Found', $message, Response::HTTP_NOT_FOUND);
  }

  protected function respondWithError(string $title, string $detail, int $status = 400): JsonResponse
  {
    $error = [
      'errors' => [
        [
          'status' => (string) $status,
          'title' => $title,
          'detail' => $detail,
        ],
      ],
    ];

    return new JsonResponse($error, $status);
  }
}
