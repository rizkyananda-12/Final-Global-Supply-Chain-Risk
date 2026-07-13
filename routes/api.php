<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SupplyChainApiController;

Route::get('/countries', [SupplyChainApiController::class, 'getCountries']);
Route::get('/risk', [SupplyChainApiController::class, 'getRiskScore']);
Route::get('/ports', [SupplyChainApiController::class, 'getPorts']);