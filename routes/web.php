<?php

use App\Jobs\SuccessPaymentStoreJob;
use App\Models\Transaction;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    $transaction = Transaction::where('code', 'INV/20230407/IV/VII/00001')
        ->with([
            'store_transactions' => function ($query) {
                $query->with(['store.user', 'items']);
            }
        ])
        ->first();

    dispatch(new SuccessPaymentStoreJob($transaction));
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified'
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
