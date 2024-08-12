<?php

declare(strict_types=1);

namespace App\User\Infrastructure;

use App\Shared\Domain\Id;
use App\User\Shared\UserBagInterface;

class UserProvider implements UserBagInterface
{
    public function getAuthUserId(): ?Id
    {
        return new Id(
            '331751d0-30dd-4196-b79c-3166064fc273 ' // TODO get user id based on token
        );

        // or return null if user is not authenticated
    }
}
