<?php

namespace App\Common;

abstract class DomainEvent
{
    public function __construct(
        public readonly string $name
    ) {
    }
}