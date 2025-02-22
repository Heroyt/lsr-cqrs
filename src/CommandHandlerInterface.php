<?php
declare(strict_types=1);

namespace Lsr\CQRS;

interface CommandHandlerInterface
{

    public function handle(CommandInterface $command) : void;

}