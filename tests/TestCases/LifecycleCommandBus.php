<?php

declare(strict_types=1);

namespace TestCases;

use Lsr\Core\App;
use Lsr\CQRS\CommandBus;
use Lsr\CQRS\CommandHandlerInterface;
use Lsr\CQRS\CommandInterface;

final class LifecycleCommandBus extends CommandBus
{
    public function __construct(App $app, private readonly CommandHandlerInterface $handler)
    {
        parent::__construct($app);
    }

    public function getHandler(CommandInterface $command): CommandHandlerInterface
    {
        return $this->handler;
    }
}
