<?php

namespace App\Modules\Cart\Domain;

use App\Modules\Cart\Contracts\CartContract;
use App\Modules\Cart\Contracts\CartData;
use App\Modules\Cart\Contracts\CartItemData;
use App\Modules\Cart\Domain\Models\Cart;
use App\Modules\Cart\Domain\Models\CartItem;

final class CartService implements CartContract
{
    public function forUser(int $userId): CartData
    {
        $cart = Cart::with('items')->firstWhere('user_id', $userId);

        $items = $cart === null ? [] : $cart->items
            ->map(fn (CartItem $item) => new CartItemData($item->product_id, $item->quantity))
            ->all();

        return new CartData($items);
    }

    public function clear(int $userId): void
    {
        Cart::firstWhere('user_id', $userId)?->items()->delete();
    }
}
