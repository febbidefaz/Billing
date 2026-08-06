<?php

use App\Http\Controllers\LabController;
use Illuminate\Support\Facades\Route;

Route::prefix('laboratorium')
    ->name('lab.')
    ->group(function () {
        Route::get('/{idLab}/print', [LabController::class, 'print'])
            ->whereNumber('idLab')
            ->name('print');
    });