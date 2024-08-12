<?php

namespace App\User\Shared;

use App\Shared\Domain\Id;

interface UserBagInterface
{
    public function getAuthUserId(): ?Id;
}
