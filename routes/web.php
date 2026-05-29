<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RawatInapController;
use App\Http\Controllers\RawatJalanController;
use App\Http\Controllers\UserBillingController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Auth::routes();

Route::get('/', function () {
    return redirect()->route('rawatinap.index');
});

Route::middleware(['auth'])->group(function () {

    Route::get('/rawat-inap', [RawatInapController::class, 'index'])->name('rawatinap.index');
    Route::get('/rawat-inap-data', [RawatInapController::class, 'data'])->name('rawatinap.data');
    Route::get('/rawat-inap/{id}', [RawatInapController::class, 'detail'])->name('rawatinap.detail');
    Route::post('/rawat-inap/{id}/update-pxrs', [RawatInapController::class, 'updatePxRS'])
        ->name('rawatinap.updatePxRS');
    Route::get('/rawat-inap/{id}/rekening-print', [RawatInapController::class, 'rekeningPrint'])
        ->name('rawatinap.rekeningPrint');
    Route::get('/rawat-jalan', [RawatJalanController::class, 'index'])->name('rawatjalan.index');
    Route::get('/rawat-jalan-data', [RawatJalanController::class, 'data'])->name('rawatjalan.data');
    Route::get('/sep/detail', [RawatInapController::class, 'sepDetail'])->name('sep.detail');
    Route::get('/bpjs/peserta', [RawatInapController::class, 'cekPesertaBpjs'])->name('bpjs.peserta');
    
    //User Billing
    Route::get('/user-billing', [UserBillingController::class, 'index'])->name('userbilling.index');
    Route::post('/user-billing/store', [UserBillingController::class, 'store'])->name('userbilling.store');
    Route::put('/userbilling/{id}', [UserBillingController::class, 'update'])->name('userbilling.update');
    Route::delete('/userbilling/{id}', [UserBillingController::class, 'destroy'])->name('userbilling.destroy');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])
    ->name('profile.password.update');
});

Route::get('/home', function () {
    return redirect()->route('rawatinap.index');
})->name('home');