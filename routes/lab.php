<?php

use App\Http\Controllers\LabController;
use App\Http\Controllers\RawatInapController;
use Illuminate\Support\Facades\Route;

Route::prefix('laboratorium')
    ->name('lab.')
    ->group(function () {
        Route::get('/{idLab}/print', [LabController::class, 'print'])
            ->whereNumber('idLab')
            ->name('print');
    });

Route::post('/laboratorium/edit/update', [RawatInapController::class, 'updateLabEdit']
    )->name('lab.edit.update');

Route::post('/laboratorium/edit/delete', [RawatInapController::class, 'deleteLabEdit']
    )->name('lab.edit.delete');

Route::post('/laboratorium/edit/sync/{id}', [RawatInapController::class, 'syncLabEdit']
    )->name('lab.edit.sync');  
  
Route::get('/lab/edit/print/{id}', [LabController::class, 'printEditLab']
    )->name('lab.edit.print');
    