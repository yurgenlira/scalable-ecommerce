<?php

namespace App\Contracts;

use App\Models\Order;
use App\Services\Payment\PaymentResult;

interface PaymentGateway
{
    public function charge(Order $order): PaymentResult;
}
