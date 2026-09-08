<?php

declare(strict_types=1);

namespace TestCases;

use Lsr\CQRS\Lifecycle\CommandLifecycleScopeInterface;
use RuntimeException;
use Throwable;

final class RecordingCommandScope implements CommandLifecycleScopeInterface
{
    public ?Throwable $exception = null;
    public int $completed = 0;
    public bool $throwOnComplete = false;

    public function recordException(Throwable $exception): void {
        $this->exception = $exception;
    }

    public function complete(): void {
        ++$this->completed;
        if ($this->throwOnComplete) {
            throw new RuntimeException('scope failed');
        }
    }
}
