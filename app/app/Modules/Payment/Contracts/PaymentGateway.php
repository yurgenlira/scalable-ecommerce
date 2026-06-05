<?php

namespace App\Modules\Payment\Contracts;

use App\Modules\Payment\Domain\PaymentResult;

interface PaymentGateway
{
    public function charge(int $amountCents): PaymentResult;
}
