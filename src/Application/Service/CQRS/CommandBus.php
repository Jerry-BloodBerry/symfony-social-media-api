<?php

namespace App\Application\Service\CQRS;

use Symfony\Component\Messenger\MessageBusInterface;

final class CommandBus implements CommandBusInterface
{
  private MessageBusInterface $commandBus;

  public function __construct(MessageBusInterface $commandBus)
  {
    $this->commandBus = $commandBus;
  }

  public function dispatch(CommandInterface $command): void
  {
    $this->commandBus->dispatch($command);
  }
}
