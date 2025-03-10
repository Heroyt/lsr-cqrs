<?php
declare(strict_types=1);

namespace Lsr\CQRS;

interface AsyncCommandBusInterface
{

    /**
     * @template T of mixed
     * @param  CommandInterface<T>  $command
     */
    public function dispatch(CommandInterface $command) : void;
}