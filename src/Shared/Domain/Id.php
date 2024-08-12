<?php

declare(strict_types=1);

namespace App\Shared\Domain;

class Id
{
    public function __construct(
        private readonly string $id
    ) {
    }

    public function toString(): string
    {
        return $this->id;
    }
}
