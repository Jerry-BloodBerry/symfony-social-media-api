<?php

namespace App\Controller;

use App\Utils\HealthCheckResponse;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class HealthCheckController extends BaseApiController
{
  #[Route('/api/health', methods: ['GET'])]
  #[OA\Response(
    response: 200,
    description: 'Check application health',
    content: new OA\JsonContent(
      type: 'array',
      items: new OA\Items(ref: new Model(type: HealthCheckResponse::class))
    )
  )]
  #[OA\Tag(name: 'HealthCheck')]
  public function health(): JsonResponse
  {
    return $this->json(['message' => 'Healthy!']);
  }
}
