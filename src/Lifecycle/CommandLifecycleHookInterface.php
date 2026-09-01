<?php

declare(strict_types=1);

namespace Lsr\CQRS\Lifecycle;

use Lsr\CQRS\CommandInterface;

interface CommandLifecycleHookInterface
{
    /**
     * @template T of mixed
     * @param CommandInterface<T> $command
     */
    public function begin(CommandInterface $command): CommandLifecycleScopeInterface;
}
