<?php

namespace App\Modules\Payment\Contracts;

use App\Modules\Ordering\Domain\Models\Order;
use App\Modules\Payment\Domain\PaymentResult;

interface PaymentGateway
{
    public function charge(Order $order): PaymentResult;
}
