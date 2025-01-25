<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MidtransCallbackController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::redirect('/', '/kokakos/login');

// Payment Gateway Routes
Route::post('payment-callback', [MidtransCallbackController::class, 'handle'])
    ->name('payment.callback');

