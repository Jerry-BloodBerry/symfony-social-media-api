<?php

namespace App\Tests\Stub;

use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Jwt\TokenFactoryInterface;
use Symfony\Component\Mercure\Jwt\TokenProviderInterface;
use Symfony\Component\Mercure\Update;

class HubStub implements HubInterface
{

    public function publish(Update $update): string
    {
        return 'id';
    }

    public function getUrl(): string
    {
        return 'url';
    }

    public function getPublicUrl(): string
    {
        return 'publicUrl';
    }

    public function getProvider(): TokenProviderInterface
    {
        return new class implements TokenProviderInterface {
            public function getJwt(): string
            {
                return 'jwtToken';
            }
        };
    }

    public function getFactory(): TokenFactoryInterface|null
    {
        return null;
    }

}