<?php

namespace App\Controller;

use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
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
  }

  /**
   * @param array<string,string> $headers
   */
  protected function respondWithItem(
    mixed $data,
    callable|TransformerAbstract|null $transformer,
    int $status = Response::HTTP_OK,
    array $headers = []
  ): JsonResponse {
    $resource = new Item($data, $transformer);
    $rootScope = $this->fractal->createData($resource);

    return new JsonResponse($rootScope->toArray(), $status, $headers);
  }

  /**
   * @param array<string,string> $headers
   */
  protected function respondWithCollection(
    mixed $data,
    callable|TransformerAbstract|null $transformer,
    int $status = Response::HTTP_OK,
    array $headers = []
  ): JsonResponse {
    $resource = new Collection($data, $transformer);
    $rootScope = $this->fractal->createData($resource);

    return new JsonResponse($rootScope->toArray(), $status, $headers);
  }

  /**
   * @param array<string,string> $headers
   */
  protected function respondWithCreated(
    mixed $data,
    callable|TransformerAbstract|null $transformer,
    array $headers = []
  ): JsonResponse {
    $resource = new Item($data, $transformer);
    $rootScope = $this->fractal->createData($resource);

    return new JsonResponse($rootScope->toArray(), Response::HTTP_CREATED, $headers);
  }

  protected function respondWithNotFound(string $message = 'Not Found'): JsonResponse
  {
    return new JsonResponse(['error' => $message], Response::HTTP_NOT_FOUND);
  }

  /**
   * @param array<string> $errors
   */
  protected function error(array $errors, int $status = JsonResponse::HTTP_BAD_REQUEST): JsonResponse
  {
    return $this->json(['errors' => $errors], $status);
  }
}