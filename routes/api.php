<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\WalletController;
use App\Http\Middleware\IsAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/user', function (Request $request) {
    return response()->json(['user' => $request->user()]);
})->middleware('auth:api');

Route::middleware('auth:api')->group(function () {
    Route::post('/credit', [WalletController::class, 'credit']);
    Route::post('/debit', [WalletController::class, 'debit']);
    Route::get('/transactions', [WalletController::class, 'getTransactions']);

    Route::middleware([IsAdmin::class])->group(function () {
        Route::post('/admin/credit', [AdminController::class, 'credit']);
        Route::post('/admin/debit', [AdminController::class, 'debit']);
        Route::get('/admin/transactions', [AdminController::class, 'weeklyReport']);
    });
});
