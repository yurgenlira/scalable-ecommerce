<?php

namespace App\Modules\Ordering\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Cart\Contracts\CartContract;
use App\Modules\Catalog\Contracts\CatalogContract;
use App\Modules\Ordering\Domain\Models\Order;
use App\Modules\Payment\Contracts\PaymentGateway;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly CartContract $cart,
        private readonly CatalogContract $catalog,
        private readonly PaymentGateway $payment,
    ) {}

    public function store(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        $cart = $this->cart->forUser($userId);

        if ($cart->items === []) {
            throw ValidationException::withMessages(['cart' => ['Cart is empty.']]);
        }

        $order = DB::transaction(function () use ($userId, $cart): Order {
            $order = Order::create(['user_id' => $userId, 'status' => 'pending', 'total_cents' => 0]);

            $totalCents = 0;
            foreach ($cart->items as $item) {
                $product = $this->catalog->find($item->productId);
                if ($product === null) {
                    throw ValidationException::withMessages(['cart' => ['Product not available.']]);
                }
                if ($item->quantity > $product->stock) {
                    throw ValidationException::withMessages(['stock' => ["Not enough stock for {$product->name}."]]);
                }

                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $item->quantity,
                    'unit_price_cents' => $product->priceCents,
                ]);
                $totalCents += $product->priceCents * $item->quantity;
            }

            $result = $this->payment->charge($totalCents);

            if ($result->approved) {
                foreach ($cart->items as $item) {
                    $this->catalog->decrementStock($item->productId, $item->quantity);
                }
                $this->cart->clear($userId);
            }

            $order->update([
                'status' => $result->approved ? 'paid' : 'payment_failed',
                'total_cents' => $totalCents,
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
