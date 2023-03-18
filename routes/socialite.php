<?php

use App\Http\Controllers\API\SocialiteController;
use Illuminate\Support\Facades\Route;

Route::post('/{driver}', SocialiteController::class);
