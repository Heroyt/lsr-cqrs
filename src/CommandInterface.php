<?php
declare(strict_types=1);

namespace Lsr\CQRS;

interface CommandInterface
{

    /**
     * @return non-empty-string Handler class or DI service name
     */
    public function getHandler() : string;

}