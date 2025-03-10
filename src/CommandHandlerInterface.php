<?php
declare(strict_types=1);

namespace Lsr\CQRS;

interface CommandHandlerInterface
{

    /**
     * @template T of mixed
     * @param  CommandInterface<T>  $command
     * @return T
     */
    public function handle(CommandInterface $command) : mixed;

}