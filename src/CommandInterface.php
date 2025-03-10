<?php
declare(strict_types=1);

namespace Lsr\CQRS;

/**
 * @template T of mixed
 */
interface CommandInterface
{

    /**
     * @return non-empty-string Handler class or DI service name
     */
    public function getHandler() : string;

}