<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/env-check', function () {
    return [
        'TWILIO_SID' => env('TWILIO_SID'),
        'TWILIO_TOKEN' => env('TWILIO_TOKEN'),
        'TWILIO_FROM' => env('TWILIO_FROM'),
    ];
});
