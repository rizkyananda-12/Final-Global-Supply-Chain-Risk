<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SupplyChainApiController;

// Mendaftarkan Endpoint REST API Platform
Route::get('/countries', [SupplyChainApiController::class, 'getCountries']);
Route::get('/risk', [SupplyChainApiController::class, 'getRiskScore']);
Route::get('/ports', [SupplyChainApiController::class, 'getPorts']);