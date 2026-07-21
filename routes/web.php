<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SupplyChainApiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminController;
use App\Http\Middleware\AdminMiddleware;
use App\Models\Port;
use Illuminate\Http\Request;

Route::get('/', function () {
    return redirect()->route('dashboard');
});
Route::get('/api/compare-countries', [SupplyChainApiController::class, 'compareCountries']);
Route::get('/api/country/{iso}', [DashboardController::class, 'getCountryData']);
Route::get('/api/countries', [SupplyChainApiController::class, 'getCountries']);
Route::get('/api/risk', [SupplyChainApiController::class, 'getRiskScore']);
Route::get('/api/ports', [SupplyChainApiController::class, 'getPorts']);
Route::get('/api/ports/{country_code}', function ($country_code) {
    $ports = Port::query()->where('country_code', $country_code)->get();
    return response()->json($ports);
});
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/api/country/{iso}', [DashboardController::class, 'getCountryData']);
    Route::middleware([AdminMiddleware::class])->group(function () {
        
        Route::get('/admin/manage-data', [AdminController::class, 'index'])->name('admin.manage');
        Route::delete('/admin/country/{id}', [AdminController::class, 'destroyCountry'])->name('admin.country.delete');
        Route::post('/admin/user/role/{id}', [AdminController::class, 'changeRole'])->name('admin.user.role');
    });
});