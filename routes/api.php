<?php

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Public
Route::post('/get-token', [ApiController::class, 'getToken']);

// Protected
Route::middleware('api.token')->group(function () {
    Route::get('/pasien-pulang', [ApiController::class, 'pasienPulang']);
    Route::get('/igd', [ApiController::class, 'igd']);
    Route::get('/rawat-jalan', [ApiController::class, 'rawatJalan']);
    Route::get('/spesialis', [ApiController::class, 'spesialis']);
    Route::get('/dokter', [ApiController::class, 'dokter']);
    Route::get('/akun-all', [ApiController::class, 'akunAll']);
});