<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// default
// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
Route::prefix('users')->group(function () {
    Route::get('/recalculate', [UserController::class, 'recalculate'])->name('users.recalculate');
    Route::get('/generate', [UserController::class, 'generate'])->name('users.generate');
    Route::post('/points', [UserController::class, 'addPoint'])->name('users.addPoint');
    Route::get('/{id?}', [UserController::class, 'users'])->name('users.index');
});
