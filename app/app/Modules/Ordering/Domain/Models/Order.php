<?php

namespace App\Modules\Ordering\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = ['user_id', 'status', 'total_cents', 'payment_reference'];

    protected $casts = ['total_cents' => 'integer'];

    /** @return HasMany<OrderItem, $this> */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
