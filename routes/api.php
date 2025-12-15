<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BagageConsigneApiController;
use App\Http\Controllers\FrontController; // Add this import

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/ping', function () {
    return response()->json([
        'success' => true,
        'message' => 'API OK ✅'
    ]);
});

Route::post('/reservations', [BagageConsigneApiController::class, 'store']);
Route::get('/reservations', [BagageConsigneApiController::class, 'index']);

// New route for getting options quote
Route::post('/commande/options-quote', [FrontController::class, 'getOptionsQuote']);