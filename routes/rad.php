<?php

use App\Http\Controllers\RadController;
use Illuminate\Support\Facades\Route;

Route::prefix('radiologi')
    ->name('rad.')
    ->group(function () {
        Route::get('/{idRad}/print', [RadController::class, 'print'])
            ->whereNumber('idRad')
            ->name('print');
    });