<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SupplyChainApiController;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/api/countries', [SupplyChainApiController::class, 'getCountries']);
Route::get('/api/risk', [SupplyChainApiController::class, 'getRiskScore']);
Route::get('/api/ports', [SupplyChainApiController::class, 'getPorts']);
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::middleware(['auth'])->group(function () {
    Route::get('/', function () {
        return view('welcome');
    });
    
    Route::get('/dashboard', function () {
        return view('welcome');
    });
});