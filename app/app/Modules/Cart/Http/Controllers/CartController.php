<?php

namespace App\Modules\Cart\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Cart\Domain\Models\CartItem;
use App\Modules\Catalog\Domain\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CartController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $cart = $request->user()->cart()->firstOrCreate([]);
        $cart->load('items.product');

        return response()->json(['items' => $cart->items, 'total_cents' => $cart->totalCents()]);
    }

    public function addItem(Request $request): JsonResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $product = Product::findOrFail($data['product_id']);
        $cart = $request->user()->cart()->firstOrCreate([]);
        $item = $cart->items()->firstOrNew(['product_id' => $product->id]);
        $desired = ($item->quantity ?? 0) + $data['quantity'];

        if ($desired > $product->stock) {
            throw ValidationException::withMessages(['quantity' => ['Not enough stock available.']]);
        }

        $item->quantity = $desired;
        $item->save();

        return response()->json(['total_cents' => $cart->fresh()->totalCents()], 201);
    }

    public function removeItem(Request $request, CartItem $item): JsonResponse
    {
        abort_unless($item->cart->user_id === $request->user()->id, 403);
        $item->delete();

        return response()->json(['status' => 'removed']);
    }
}
