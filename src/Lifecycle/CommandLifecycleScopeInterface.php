<?php

declare(strict_types=1);

namespace Lsr\CQRS\Lifecycle;

use Throwable;

interface CommandLifecycleScopeInterface
{
    public function recordException(Throwable $exception): void;

    public function complete(): void;
}
