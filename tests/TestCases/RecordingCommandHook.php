<?php

declare(strict_types=1);

namespace TestCases;

use Lsr\CQRS\CommandInterface;
use Lsr\CQRS\Lifecycle\CommandLifecycleHookInterface;
use Lsr\CQRS\Lifecycle\CommandLifecycleScopeInterface;
use RuntimeException;

final class RecordingCommandHook implements CommandLifecycleHookInterface
{
    public ?object $command = null;
    public bool $throwOnBegin = false;

    public function __construct(private readonly CommandLifecycleScopeInterface $scope) {
    }

    public function begin(CommandInterface $command): CommandLifecycleScopeInterface {
        if ($this->throwOnBegin) {
            throw new RuntimeException('hook failed');
        }

        $this->command = $command;
        return $this->scope;
    }
}
