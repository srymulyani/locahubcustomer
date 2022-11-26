<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\AddressController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\TransactionController;
use App\Http\Controllers\API\ResetPasswordController;
use App\Http\Controllers\API\ForgotPasswordController;
use App\Http\Controllers\API\ProductCategoryController;
use App\Http\Controllers\API\EmailVerificationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//PRODUCTS
Route::get('products',[ProductController::class,'all']);
Route::post('products', [ProductController::class,'create']);
Route::post('updateProducts', [ProductController::class,'updateAll']);
Route::delete('products/{id}', [ProductController::class,'delete']);


//PRODUCTS CATEGORY
Route::get('category',[ProductCategoryController::class,'all']);


//USER
Route::post('register',[UserController::class,'register']);
Route::post('login',[UserController::class,'login']);
Route::post('forgot-password', [ForgotPasswordController::class,'ForgotPassword']); 
Route::post('reset-password', [ForgotPasswordController::class,'reset']);
// Route::post('reset-password', [ResetPasswordController::class,'ResetPassword' ]);


Route::middleware('auth:sanctum')->group (function () {
    Route::get('user',  [UserController::class, 'fetch'])->middleware('verified');
    Route::post('user',[UserController::class,'updateProfile']);
    Route::post('logout',[UserController::class,'logout']);
    Route::post('changePassword', [UserController::class, 'changePassword']);

    //ADDRESS
    Route::post('address/add', [AddressController::class,'create']); 
    Route::get('address/all', [AddressController::class,'all']);
    Route::post('address/edit', [AddressController::class, 'edit']);
    Route::delete('address/{id}', [AddressController::class,'destroy']);

    //BANK
    Route::post('bank', [AddressController::class,'create']); 
    Route::get('bank', [AddressController::class,'all']);
    Route::post('bank/edit', [AddressController::class, 'edit']);
    Route::delete('bank/{id}', [AddressController::class,'destroy']);

    //TRANSACTION
    Route::get('transaction', [TransactionController::class,'all']);
    Route::post('checkout', [TransactionController::class,'checkout']);
    Route::post('edit/transaction',[TransactionController::class,'edit']);

    //STORE
    Route::get('store', [StoreController::class,'show']);
    Route::post('create', [StoreController::class,'checkout']);
    Route::post('store-update',[StoreController::class,'update']);

    //VOUCHER
    Route::get('voucher', [VoucherController::class,'all']);
    Route::post('voucher', [VoucherController::class,'create']);

    //EMAIL_VERIFICATION
    Route::post('email/verification-notification', [EmailVerificationController::class, 'sendVerificationEmail']);
    Route::get('verify-email/{id}/{hash}', [EmailVerificationController::class, 'verify'])->name('verification.verify');

});




