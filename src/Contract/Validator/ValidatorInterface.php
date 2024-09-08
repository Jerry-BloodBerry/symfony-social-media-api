<?php

namespace App\Contract\Validator;

interface ValidatorInterface
{
    public function validate(mixed $data): void;
}