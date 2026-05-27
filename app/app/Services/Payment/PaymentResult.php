<?php

namespace App\Services\Payment;

class PaymentResult
{
    public function __construct(
        public readonly bool $approved,
        public readonly string $reference,
        public readonly ?string $message = null,
    ) {}
}
