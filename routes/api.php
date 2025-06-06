<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\TelegramBotController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/telegram/webhook', [TelegramBotController::class, 'webhook']);
Route::get('/get-planogram/{id}', [ApiController::class, 'index']);
Route::get('/get-product-image/{code}', ImageController::class);
Route::get('/get-shops', [ApiController::class, 'getShops']);
Route::get('/get-groups', [ApiController::class, 'getGroups']);
Route::get('/sync-barcodes', [ApiController::class, 'syncBarcodes']);