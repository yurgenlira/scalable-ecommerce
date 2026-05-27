<?php

namespace App\Services\Payment;

use App\Contracts\PaymentGateway;
use App\Models\Order;
use Illuminate\Support\Str;

class MockPaymentGateway implements PaymentGateway
{
    public function charge(Order $order): PaymentResult
    {
        $config = config('services.payment.mock');

        if ($config['latency_ms'] > 0) {
            usleep($config['latency_ms'] * 1000);
        }

        $declineOver = $config['decline_over_cents'];
        $approved = $declineOver === null || $order->total_cents <= $declineOver;

        return new PaymentResult(
            approved: $approved,
            reference: 'mock_'.Str::random(20),
            message: $approved ? null : 'Amount exceeds mock decline threshold.',
        );
    }
}
