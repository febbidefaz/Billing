<?php

use App\Http\Controllers\RadController;
use App\Http\Controllers\RawatInapController;
use Illuminate\Support\Facades\Route;

Route::prefix('radiologi')
    ->name('rad.')
    ->group(function () {
        Route::get('/{idRad}/print', [RadController::class, 'print'])
            ->whereNumber('idRad')
            ->name('print');
    });

Route::post( '/radiologi/edit/sync/{id}', [RawatInapController::class, 'syncRadiologiEdit']
    )->name('radiologi.edit.sync');
    
Route::post( '/radiologi/edit/update', [RawatInapController::class, 'updateRadiologiEdit']
    )->name('radiologi.edit.update');
    
Route::post( '/radiologi/edit/delete',  [RawatInapController::class, 'deleteRadiologiEdit']
    )->name('radiologi.edit.delete');

Route::get( '/radiologi/edit/print/{id}', [RadController::class, 'printEditRadiologi']
    )->name('radiologi.edit.print');