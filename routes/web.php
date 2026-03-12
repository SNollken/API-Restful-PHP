<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/generate-csrf-token', function () {
    return response()->json([
        'csrf_token' => csrf_token()
    ]);
});
