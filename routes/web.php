<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SupplyChainApiController;

// Rute untuk halaman web utama
Route::get('/', function () {
    return view('welcome');
});

// Pindahkan rute API ke web.php agar pasti terbaca oleh AJAX tanpa kendala middleware
Route::get('/api/countries', [SupplyChainApiController::class, 'getCountries']);
Route::get('/api/risk', [SupplyChainApiController::class, 'getRiskScore']);
Route::get('/api/ports', [SupplyChainApiController::class, 'getPorts']);