<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ObapayEditController;

Route::middleware(['auth'])->prefix('obapay')->group(function () {

    Route::get('/{id}', [ObapayEditController::class, 'index'])
        ->name('obapay.index');

    Route::post('/sync/{id}', [ObapayEditController::class, 'sync'])
        ->name('obapay.sync');

    Route::post('/update', [ObapayEditController::class, 'update'])
        ->name('obapay.update');

    Route::post('/delete', [ObapayEditController::class, 'delete'])
        ->name('obapay.delete');

    Route::get('/total/{id}', [ObapayEditController::class, 'total'])
        ->name('obapay.total');
    
    Route::get('/obapay/medicines/search', [ObapayEditController::class, 'searchMedicine'])
        ->name('obapay.medicines.search');
    
    Route::post('/obapay/store', [ObapayEditController::class, 'store'])
        ->name('obapay.store');     
});