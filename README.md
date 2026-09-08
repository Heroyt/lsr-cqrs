# LSR CQRS

`lsr/cqrs` provides a synchronous command bus backed by the LSR application's service container, an interface for application-provided asynchronous dispatch, and a small query contract.

## Requirements

- PHP `>=8.4`.
- LSR Core `^0.3 || ^0.4 || ^0.5`; LSR Interfaces, Logging and Serializer `^0.3`; Nette DI `^3.2`.
- A bootstrapped `Lsr\Core\App` and registered command-handler services.
- No PHP extensions are explicitly required by this package's manifest; its framework dependencies have their own platform requirements.

## Installation

```shell
composer require lsr/cqrs
```

## Command dispatch

Register the extension in the application's Nette DI configuration:

```neon
extensions:
    cqrs: Lsr\CQRS\DI\CqrsExtension
```

The extension expects the Core application service to be named `app` and registers an autowired `Lsr\CQRS\CommandBus`.

Implement the following contracts in application code:

1. A command implements [`CommandInterface`](src/CommandInterface.php). Its `getHandler(): string` returns either the handler's concrete class name or its DI service name.
2. The handler implements [`CommandHandlerInterface`](src/CommandHandlerInterface.php), with `handle(CommandInterface $command): mixed`. Register the handler as an application service.
3. Inject `CommandBus` and call `dispatch($command)`. The bus resolves the handler and returns its result; application exceptions propagate to the caller.

Handler lookup is not convention-based: the command explicitly selects the handler. Class-name lookup uses `App::findServicesByType()` and selects the first matching service, so avoid registering ambiguous handlers of the same type. Service-name lookup uses `App::getService()`. Missing or invalid handlers fail rather than silently dropping the command.

See [CommandBus](src/CommandBus.php) for the complete dispatch and handler-resolution contract and [CqrsExtension](src/DI/CqrsExtension.php) for DI wiring. `CommandInterface<T>` and the bus's PHPDoc generics describe the returned value to static analysis; they do not enforce a runtime result type.

## Asynchronous dispatch

This package does not include a queue transport. Provide and register a service implementing `Lsr\CQRS\AsyncCommandBusInterface`, then reference its service name:

```neon
cqrs:
    asyncBus: asyncCommandBus
```

Here `asyncCommandBus` must already be a service in the application's container. `CommandBus::dispatchAsync($command)` delegates to that service's `dispatch()` and returns no result. Calling it without an async bus throws `RuntimeException`. Serialization, delivery, retry and worker behavior belong to the chosen implementation, not this package.

## Queries and instrumentation

[`QueryInterface`](src/QueryInterface.php) defines only `get(): mixed`. There is no separate query bus in this package.

Synchronous commands can be observed through `CommandBus::setLifecycleHook()` and the interfaces in [src/Lifecycle](src/Lifecycle). Hook failures are isolated from command execution; handler exceptions are still rethrown.

## Development

CI runs the checks below on PHP 8.4 and 8.5. From a package checkout:

```shell
composer install --prefer-dist --no-interaction --no-progress
composer cs
vendor/bin/phpstan analyse --no-progress
vendor/bin/phpunit --no-coverage
```

`composer cs` checks coding style without changing files. Run `composer cs:fix` (or `composer cbf`) to apply PHP CS Fixer rules from [.php-cs-fixer.php](.php-cs-fixer.php).

The suite needs no external services. CI installs the framework's Redis, PDO SQLite, gettext, fileinfo, SimpleXML and ZIP extensions alongside the DOM, mbstring, XML and XMLWriter extensions used by the development tools. The Redis extension satisfies the dependency platform requirement; these tests do not connect to a Redis server.

## AI coding assistance

See [LSR Skills](https://github.com/Heroyt/lsr-skills) for AI agent skills for working with the LSR framework.

## License

Licensed under the [MIT License](LICENSE).
