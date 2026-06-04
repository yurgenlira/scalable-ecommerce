<?php

use App\Modules\Ordering\Http\Controllers\CheckoutController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->post('checkout', [CheckoutController::class, 'store']);
