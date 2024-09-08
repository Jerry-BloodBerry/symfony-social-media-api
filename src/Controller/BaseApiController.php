<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationListInterface;

abstract class BaseApiController extends AbstractController
{

  /**
   * @param array<string,string> $headers
   */
  protected function respondWithItem(
    mixed $data,
    int $status = Response::HTTP_OK,
    array $headers = [],
    bool $json = true
  ): JsonResponse {
    return new JsonResponse($data, $status, $headers, $json);
  }

  /**
   * @param array<string,string> $headers
   */
  protected function respondWithCollection(
    mixed $data,
    int $status = Response::HTTP_OK,
    array $headers = [],
    bool $json = true
  ): JsonResponse {
    return new JsonResponse($data, $status, $headers, $json);
  }

  /**
   * @param array<string,string> $headers
   */
  protected function respondCreated(
    mixed $data,
    string $location,
    array $headers = [],
    bool $json = true
  ): JsonResponse {
    $headers['Location'] = $location;
    return new JsonResponse($data, Response::HTTP_CREATED, $headers, $json);
  }

  protected function respondNoContent(array $headers = [])
  {
    return new JsonResponse(null, Response::HTTP_NO_CONTENT, $headers);
  }

  protected function respondNotFound(string $message = 'Not Found'): JsonResponse
  {
    return $this->respondWithError('Not Found', $message, Response::HTTP_NOT_FOUND);
  }

  protected function respondBadRequest(string $message = 'Bad Request'): JsonResponse
  {
    return $this->respondWithError('Bad Request', $message, Response::HTTP_BAD_REQUEST);
  }

  protected function problemDetails(
    ConstraintViolationListInterface $violations,
    int $status = Response::HTTP_BAD_REQUEST,
    string $type = null,
    string $title = "Validation Failed"
  ): JsonResponse {
    // Default type to a standard validation problem type
    $type = $type ?: 'https://example.com/validation-error';

    // Format the violations to match RFC 7807 structure
    $errors = [];
    foreach ($violations as $violation) {
      $errors[] = [
        'field' => $violation->getPropertyPath(),
        'message' => $violation->getMessage(),
      ];
    }

    // Build the response array
    $data = [
      'type' => $type,
      'title' => $title,
      'status' => $status,
      'detail' => 'There were validation errors.',
      'violations' => $errors,
    ];

    return new JsonResponse($data, $status);
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
