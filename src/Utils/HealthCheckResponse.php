<?php

namespace App\Utils;

class HealthCheckResponse
{
    public function __construct(
        public readonly string $message = 'Healthy!',
    ) {
    }
}