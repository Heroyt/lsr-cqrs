<?php

declare(strict_types=1);

namespace TestCases;

use Lsr\Core\App;
use Lsr\CQRS\CommandHandlerInterface;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class CommandBusLifecycleTest extends TestCase
{
    public function testLifecycleWrapsSuccessfulDispatch(): void
    {
        $scope = new RecordingCommandScope();
        $hook = new RecordingCommandHook($scope);
        $handler = $this->createStub(CommandHandlerInterface::class);
        $handler->method('handle')->willReturn('handled');
        $bus = new LifecycleCommandBus(
            $this->createStub(App::class),
            $handler,
        );
        $command = new LifecycleCommand();
        $bus->setLifecycleHook($hook);

        self::assertSame('handled', $bus->dispatch($command));
        self::assertSame($command, $hook->command);
        self::assertSame(1, $scope->completed);
        self::assertNull($scope->exception);
    }

    public function testLifecycleRecordsFailedDispatchAndPreservesException(): void
    {
        $expected = new RuntimeException('handler failed');
        $scope = new RecordingCommandScope();
        $handler = $this->createStub(CommandHandlerInterface::class);
        $handler->method('handle')->willThrowException($expected);
        $bus = new LifecycleCommandBus(
            $this->createStub(App::class),
            $handler,
        );
        $bus->setLifecycleHook(new RecordingCommandHook($scope));

        try {
            $bus->dispatch(new LifecycleCommand());
            self::fail('The handler exception was not rethrown.');
        } catch (RuntimeException $actual) {
            self::assertSame($expected, $actual);
        }

        self::assertSame($expected, $scope->exception);
        self::assertSame(1, $scope->completed);
    }

    public function testLifecycleFailuresNeverAffectDispatch(): void
    {
        $scope = new RecordingCommandScope();
        $scope->throwOnComplete = true;
        $hook = new RecordingCommandHook($scope);
        $handler = $this->createStub(CommandHandlerInterface::class);
        $handler->method('handle')->willReturn('handled');
        $bus = new LifecycleCommandBus(
            $this->createStub(App::class),
            $handler,
        );
        $bus->setLifecycleHook($hook);

        self::assertSame('handled', $bus->dispatch(new LifecycleCommand()));

        $hook->throwOnBegin = true;
        self::assertSame('handled', $bus->dispatch(new LifecycleCommand()));
    }
}
