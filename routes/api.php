<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::apiResource('products', App\Http\Controllers\ProductController::class);
    Route::apiResource('sales', App\Http\Controllers\SaleController::class);
    Route::get('reports/sales-summary', [App\Http\Controllers\ReportController::class, 'salesSummary']);
});
