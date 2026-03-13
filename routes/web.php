<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'status' => 'ok',
        'message' => 'API RESTful está rodando',
        'docs' => '/api/v1'
    ]);
});

Route::get('/generate-csrf-token', function () {
    return response()->json([
        'csrf_token' => csrf_token()
    ]);
});
