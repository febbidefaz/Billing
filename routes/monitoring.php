<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MonitoringController;

Route::prefix('monitoring')->group(function () {

    Route::get('/monitoring', [MonitoringController::class, 'index'])
        ->name('monitoring.ri');

    Route::get('/data', [MonitoringController::class, 'data'])
        ->name('monitoring.data');
    Route::get('/rinci/{id}', [MonitoringController::class, 'rinci'])
        ->name('monitoring.rinci');    

});