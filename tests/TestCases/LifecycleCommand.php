<?php

declare(strict_types=1);

namespace TestCases;

use Lsr\CQRS\CommandInterface;

/** @implements CommandInterface<string> */
final class LifecycleCommand implements CommandInterface
{
    public function getHandler(): string {
        return 'unused';
    }
}
