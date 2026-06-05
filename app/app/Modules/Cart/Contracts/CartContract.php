<?php

namespace App\Modules\Cart\Contracts;

interface CartContract
{
    public function forUser(int $userId): CartData;

    public function clear(int $userId): void;
}
