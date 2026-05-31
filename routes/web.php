<?php

use App\Http\Controllers\IGDController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PulangController;
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
 
    Route::get('/sep/detail', [RawatInapController::class, 'sepDetail'])->name('sep.detail');
    Route::get('/bpjs/peserta', [RawatInapController::class, 'cekPesertaBpjs'])->name('bpjs.peserta');

    Route::get('/rawatinap/{id}/obat-rinci/{roomId}', [RawatInapController::class, 'obatRinciPrint']
        )->name('rawatinap.obatRinciPrint');
    Route::get('/rawatinap/{id}/obapay-print',[RawatInapController::class, 'obapayPrint']
        )->name('rawatinap.obapayPrint');
    Route::post('/rawatinap/{id}/simpan-kasir',[RawatInapController::class, 'simpanKasir']
        )->name('rawatinap.simpanKasir');   
    Route::get('/rawatinap/{id}/kwitansi-print',[RawatInapController::class, 'kwitansiPrint']
        )->name('rawatinap.kwitansiPrint');

        // Tgl Bayar
    Route::post('/rawatinap/{id}/update-tgl-bayar',[RawatInapController::class, 'updateTglBayar']
        )->name('rawatinap.updateTglBayar');
    Route::post('/rawatinap/{id}/hapus-tgl-bayar',[RawatInapController::class, 'hapusTglBayar']
        )->name('rawatinap.hapusTglBayar');    

        // pasInap
    Route::post('/rawatinap/update-pasinap',[RawatInapController::class, 'updatePasInap']
        )->name('rawatinap.updatePasInap');
    Route::post('/rawatinap/insert-pasinap',[RawatInapController::class, 'insertPasInap']
        )->name('rawatinap.insertPasInap');
    Route::post('/rawatinap/delete-pasinap',[RawatInapController::class, 'deletePasInap']
        )->name('rawatinap.deletePasInap'); 

        // Biaya Lain
    Route::post('/rawatinap/insert-lain', [RawatInapController::class, 'insertLain'])
        ->name('rawatinap.insertLain');   
    Route::post('/rawatinap/update-lain', [RawatInapController::class, 'updateLain'])
        ->name('rawatinap.updateLain');
    Route::post('/rawatinap/delete-lain', [RawatInapController::class, 'deleteLain'])
    ->name('rawatinap.deleteLain');

    // User Billing
    Route::get('/user-billing', [UserBillingController::class, 'index'])->name('userbilling.index');
    Route::post('/user-billing/store', [UserBillingController::class, 'store'])->name('userbilling.store');
    Route::put('/userbilling/{id}', [UserBillingController::class, 'update'])->name('userbilling.update');
    Route::delete('/userbilling/{id}', [UserBillingController::class, 'destroy'])->name('userbilling.destroy');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])
        ->name('profile.password.update');

        // Rawat Jalan
    Route::get('/rawatjalan', [RawatJalanController::class, 'index'])->name('rawatjalan');
    Route::get('/rawatjalan/data', [RawatJalanController::class, 'data'])->name('rawatjalan.data');
    Route::get('/rawatjalan/{id}', [RawatJalanController::class, 'detail'])->name('rawatjalan.detail');
    Route::get('/rawatjalan/{id}/rekening-print', [RawatJalanController::class, 'rekeningPrint'])
        ->name('rawatjalan.rekeningPrint');
        
       // IGD
    Route::get('/igd', [IGDController::class, 'index'])->name('igd');      
    Route::get('/igd/data', [IGDController::class, 'data'])->name('igd.data');  
    Route::get('/igd/{id}', [IGDController::class, 'detail'])->name('igd.detail');
    Route::get('/igd/{id}/rekening-print', [IGDController::class, 'rekeningPrint'])
        ->name('igd.rekeningPrint');  

        // Pasien Pulang
    Route::get('/pulang', [PulangController::class, 'index'])->name('pulang');
    Route::get('/pulang/data', [PulangController::class, 'data'])->name('pulang.data');
    Route::get('/pulang/{id}', [PulangController::class, 'detail'])->name('pulang.detail');
});

Route::get('/home', function () {
    return redirect()->route('rawatinap.index');
})->name('home');