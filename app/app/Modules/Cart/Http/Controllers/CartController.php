<?php

namespace App\Modules\Cart\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Cart\Domain\Models\Cart;
use App\Modules\Cart\Domain\Models\CartItem;
use App\Modules\Catalog\Contracts\CatalogContract;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CartController extends Controller
{
    public function __construct(private readonly CatalogContract $catalog) {}

    public function show(Request $request): JsonResponse
    {
        $cart = Cart::firstOrCreate(['user_id' => $request->user()->id]);
        $cart->load('items');

        return response()->json(['items' => $cart->items, 'total_cents' => $this->totalCents($cart)]);
    }

    public function addItem(Request $request): JsonResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $product = $this->catalog->find($data['product_id']);
        if ($product === null) {
            throw ValidationException::withMessages(['product_id' => ['Product not found.']]);
        }

        $cart = Cart::firstOrCreate(['user_id' => $request->user()->id]);
        $item = $cart->items()->firstOrNew(['product_id' => $product->id]);
        $desired = ($item->quantity ?? 0) + $data['quantity'];

        if ($desired > $product->stock) {
            throw ValidationException::withMessages(['quantity' => ['Not enough stock available.']]);
        }

        $item->quantity = $desired;
        $item->save();

        return response()->json(['total_cents' => $this->totalCents($cart->fresh())], 201);
    }

    public function removeItem(Request $request, CartItem $item): JsonResponse
    {
        abort_unless($item->cart->user_id === $request->user()->id, 403);
        $item->delete();

        return response()->json(['status' => 'removed']);
    }

    private function totalCents(Cart $cart): int
    {
        return $cart->items->sum(function (CartItem $item): int {
            $product = $this->catalog->find($item->product_id);

            return $product === null ? 0 : $item->quantity * $product->priceCents;
        });
    }
}
