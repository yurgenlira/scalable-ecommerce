<?php

namespace App\Modules\Ordering\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Payment\Contracts\PaymentGateway;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    public function store(Request $request, PaymentGateway $gateway): JsonResponse
    {
        $cart = $request->user()->cart()->with('items.product')->first();

        if (! $cart || $cart->items->isEmpty()) {
            throw ValidationException::withMessages(['cart' => ['Cart is empty.']]);
        }

        $order = DB::transaction(function () use ($request, $cart, $gateway) {
            foreach ($cart->items as $item) {
                if ($item->quantity > $item->product->stock) {
                    throw ValidationException::withMessages(['stock' => ["Not enough stock for {$item->product->name}."]]);
                }
            }

            $order = $request->user()->orders()->create([
                'status' => 'pending',
                'total_cents' => $cart->totalCents(),
            ]);

            foreach ($cart->items as $item) {
                $order->items()->create([
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'unit_price_cents' => $item->product->price_cents,
                ]);
            }

            $result = $gateway->charge($order);

            if ($result->approved) {
                foreach ($cart->items as $item) {
                    $item->product->decrement('stock', $item->quantity);
                }
                $cart->items()->delete();
            }

            $order->update([
                'status' => $result->approved ? 'paid' : 'payment_failed',
                'payment_reference' => $result->reference,
            ]);

            return $order;
        });

        return response()->json([
            'id' => $order->id,
            'status' => $order->status,
            'total_cents' => $order->total_cents,
        ], 201);
    }
}
