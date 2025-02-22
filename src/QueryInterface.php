<?php
declare(strict_types=1);

namespace Lsr\CQRS;

interface QueryInterface
{

    public function get() : mixed;

}