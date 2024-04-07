<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class HealthCheckController extends BaseApiController
{
  #[Route('/api/health')]
  public function health(): JsonResponse
  {
    return $this->json(['message' => 'Healthy!']);
  }
}
