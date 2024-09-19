<?php

namespace App\Common\Response;

class ProblemDetailsViolation
{
    public function __construct(
        public readonly string $field,
        public readonly string $message
    ) {
    }
}