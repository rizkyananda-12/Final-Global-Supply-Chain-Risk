<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SupplyChainApiController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/api/countries', [SupplyChainApiController::class, 'getCountries']);
Route::get('/api/risk', [SupplyChainApiController::class, 'getRiskScore']);
Route::get('/api/ports', [SupplyChainApiController::class, 'getPorts']);