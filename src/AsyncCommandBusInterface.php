<?php
declare(strict_types=1);

namespace Lsr\CQRS;

interface AsyncCommandBusInterface
{

    public function dispatch(CommandInterface $command) : void;
}