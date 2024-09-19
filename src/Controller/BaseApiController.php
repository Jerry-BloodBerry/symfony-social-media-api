<?php

namespace App\Controller;

use App\Common\Response\ProblemDetails;
use App\Common\Response\ProblemDetailsViolation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationList;
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

  protected function respondNotFound(string $message = 'NotFound', string $detail = 'Resource was not found on the server.'): JsonResponse
  {
    return $this->problemDetails(new ConstraintViolationList(), Response::HTTP_NOT_FOUND, 'Not Found', $message, $detail);
  }

  protected function respondBadRequest(string $message = 'BadRequest', string $detail = 'Request is considered invalid.'): JsonResponse
  {
    return $this->problemDetails(new ConstraintViolationList(), Response::HTTP_NOT_FOUND, 'Bad Request', $message, $detail);
  }

  protected function respondConflict(string $message = 'Conflict', string $detail = 'A conflict occurred on the server.'): JsonResponse
  {
    return $this->problemDetails(new ConstraintViolationList(), Response::HTTP_CONFLICT, 'Conflict', $message, $detail);
  }

  protected function problemDetails(
    ConstraintViolationListInterface $violations,
    int $status = Response::HTTP_BAD_REQUEST,
    string $type = "Validation",
    string $title = "Validation Failed",
    string $detail = 'There were validation errors.'
  ): JsonResponse {
    // Format the violations to match RFC 7807 structure
    $errors = [];
    foreach ($violations as $violation) {
      $errors[] = new ProblemDetailsViolation(
        $violation->getPropertyPath(),
        $violation->getMessage()
      );
    }
    $problemDetails = new ProblemDetails($type, $title, $status, $detail, $errors);

    return new JsonResponse($problemDetails, $status);
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
