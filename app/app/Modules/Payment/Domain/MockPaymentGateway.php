<?php

namespace App\Modules\Payment\Domain;

use App\Modules\Payment\Contracts\PaymentGateway;
use Illuminate\Support\Str;

class MockPaymentGateway implements PaymentGateway
{
    public function charge(int $amountCents): PaymentResult
    {
        $config = config('services.payment.mock');

        if ($config['latency_ms'] > 0) {
            usleep($config['latency_ms'] * 1000);
        }

        $declineOver = $config['decline_over_cents'];
        $approved = $declineOver === null || $amountCents <= $declineOver;

        return new PaymentResult(
            approved: $approved,
            reference: 'mock_'.Str::random(20),
            message: $approved ? null : 'Amount exceeds mock decline threshold.',
        );
    }
}
