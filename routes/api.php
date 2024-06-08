<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

Route::controller(RegisterController::class)->group(function(){
    Route::post('register', 'register');
    Route::post('login', 'login');
    Route::post('logout', 'logout');
});
// Route::post('/logout', [AuthenticatedSessionController::class, 'destory'])->name('destory');
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::options('/{any}', function () {
    return response()->json([], 200);
})->where('any', '.*');

Route::middleware('auth:sanctum')->group( function () {
    Route::resource('customers', CustomerController::class);
    Route::resource('documents', DocumentController::class);
    Route::get('/documents/{id}/download', [DocumentController::class, 'download'])->name('download');    
});
         

