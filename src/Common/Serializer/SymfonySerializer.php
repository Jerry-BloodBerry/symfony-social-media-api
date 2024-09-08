<?php

namespace App\Common\Serializer;

use App\Contract\Serializer\SerializerInterface;
use GBProd\UuidNormalizer\UuidDenormalizer;
use GBProd\UuidNormalizer\UuidNormalizer;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\UidNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface as SymfonySerializerInterface;

class SymfonySerializer implements SerializerInterface
{
    private SymfonySerializerInterface $serializer;

    public function __construct()
    {
        $this->serializer = new Serializer(
            [
                new UuidNormalizer(),
                new UuidDenormalizer(),
                new DateTimeNormalizer([DateTimeNormalizer::TIMEZONE_KEY => 'UTC']), // For \DateTime objects
                new ArrayDenormalizer(), // For handling collections
                new ObjectNormalizer(
                    classMetadataFactory: new ClassMetadataFactory(new AttributeLoader()),
                    propertyAccessor: new PropertyAccessor()
                )
            ],
            [new JsonEncoder()]
        );
    }

    public function serialize(mixed $data, string $format, array $context = []): string
    {
        return $this->serializer->serialize($data, $format, $context);
    }

    public function deserialize(mixed $data, string $type, string $format, array $context = []): mixed
    {
        return $this->serializer->deserialize($data, $type, $format, $context);
    }
}