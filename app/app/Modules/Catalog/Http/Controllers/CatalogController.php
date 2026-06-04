<?php

namespace App\Modules\Catalog\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Catalog\Domain\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CatalogController extends Controller
{
    /** @return LengthAwarePaginator<int, Product> */
    public function index(): LengthAwarePaginator
    {
        return Product::query()->orderBy('name')->paginate(15);
    }
}
