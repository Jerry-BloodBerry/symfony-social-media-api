<?php

namespace App\Common\Response;

class ProblemDetails
{
    /**
     * Initializes a new instance of the ProblemDetails class.
     *
     * @param string $type The type of problem.
     * @param string $title The title of the problem.
     * @param int $status The HTTP status code associated with the problem.
     * @param string $detail A human-readable explanation specific to this occurrence of the problem.
     * @param array<ProblemDetailsViolation> $violations A list of validation error details.
     */
    public function __construct(
        public readonly string $type,
        public readonly string $title,
        public readonly int $status,
        public readonly string $detail,
        public readonly array $violations,
    ) {
    }
}