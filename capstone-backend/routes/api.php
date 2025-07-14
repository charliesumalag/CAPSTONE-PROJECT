<?php

use App\Http\Controllers\Api\DataController;
use Illuminate\Support\Facades\Route;

Route::middleware('api')->group(function () {
    Route::get('/', [DataController::class, 'index']);
    Route::get('/readings', [DataController::class, 'latest']);
    Route::post('/readings', [DataController::class, 'store']);
    // Route::get('/test-twilio-env', [DataController::class, 'checkTwilioEnv']);
});
