<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure;

use App\Shared\Application\IdGeneratorInterface;
use App\Shared\Domain\Id;
use Ramsey\Uuid\Uuid;

class UuidGenerator implements IdGeneratorInterface
{
    public function generate(): Id
    {
        return new Id(Uuid::uuid4()->toString());
    }
}
