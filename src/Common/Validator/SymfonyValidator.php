<?php

namespace App\Common\Validator;

use App\Contract\Validator\ValidatorInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface as SymfonyValidatorInterface;

class SymfonyValidator implements ValidatorInterface
{
    private SymfonyValidatorInterface $validator;

    public function __construct(SymfonyValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validate(mixed $data): void
    {
        $violations = $this->validator->validate($data);

        if (count($violations) > 0) {
            throw new ValidationFailedException($data, $violations);
        }
    }
}