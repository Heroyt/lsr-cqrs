<?php

declare(strict_types=1);

namespace Lsr\CQRS;

use Lsr\Core\App;
use Lsr\CQRS\Lifecycle\CommandLifecycleHookInterface;
use Lsr\CQRS\Lifecycle\CommandLifecycleScopeInterface;
use RuntimeException;
use Throwable;

class CommandBus
{
    private ?CommandLifecycleHookInterface $lifecycleHook = null;

    public function __construct(
        protected readonly App $app,
        protected readonly ?AsyncCommandBusInterface $asyncCommandBus = null,
    ) {
    }

    public function setLifecycleHook(CommandLifecycleHookInterface $hook): static {
        $this->lifecycleHook = $hook;
        return $this;
    }

    /**
     * @template T of mixed
     * @param  CommandInterface<T>  $command
     */
    public function dispatchAsync(CommandInterface $command): void {
        if ($this->asyncCommandBus === null) {
            throw new RuntimeException('AsyncCommandBus is not set');
        }
        $this->asyncCommandBus->dispatch($command);
    }

    /**
     * @template T of mixed
     * @param  CommandInterface<T>  $command
     * @return T
     */
    public function dispatch(CommandInterface $command): mixed {
        $scope = $this->beginLifecycle($command);

        try {
            $handler = $this->getHandler($command);
            return $handler->handle($command);
        } catch (Throwable $exception) {
            try {
                $scope?->recordException($exception);
            } catch (Throwable) {
                // Lifecycle hooks must never affect command dispatch.
            }
            throw $exception;
        } finally {
            try {
                $scope?->complete();
            } catch (Throwable) {
                // Lifecycle hooks must never affect command dispatch.
            }
        }
    }

    /**
     * @template T of mixed
     * @param CommandInterface<T> $command
     */
    private function beginLifecycle(CommandInterface $command): ?CommandLifecycleScopeInterface {
        try {
            return $this->lifecycleHook?->begin($command);
        } catch (Throwable) {
            return null;
        }
    }

    /**
     * Find handler for command using DI
     *
     * @template T of mixed
     * @param  CommandInterface<T>  $command
     */
    public function getHandler(CommandInterface $command): CommandHandlerInterface {
        if (class_exists($command->getHandler())) {
            $services = $this->app::findServicesByType($command->getHandler());
            if (count($services) === 0) {
                throw new RuntimeException('Cannot find handler for command ' . $command::class);
            }
            $handler = first($services);
            if ( ! $handler instanceof CommandHandlerInterface) {
                throw new RuntimeException(
                    'Handler for command ' . $command::class . ' is not an instance of CommandHandlerInterface',
                );
            }
            return $handler;
        }

        $handler = $this->app::getService($command->getHandler());
        if ( ! $handler instanceof CommandHandlerInterface) {
            throw new RuntimeException(
                'Handler for command ' . $command::class . ' is not an instance of CommandHandlerInterface',
            );
        }
        return $handler;
    }
}
