<?php

namespace App\Modules\Payment\Domain;

class PaymentResult
{
    public function __construct(
        public readonly bool $approved,
        public readonly string $reference,
        public readonly ?string $message = null,
    ) {}
}
