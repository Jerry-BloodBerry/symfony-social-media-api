<?php

namespace App\Contract\Serializer;

interface SerializerInterface
{

    public function serialize(mixed $data, string $format, array $context = []): string;

    public function deserialize(mixed $data, string $type, string $format, array $context = []): mixed;
}