<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CatalogController extends Controller
{
    /** @return LengthAwarePaginator<int, Product> */
    public function index(): LengthAwarePaginator
    {
        return Product::query()->orderBy('name')->paginate(15);
    }
}