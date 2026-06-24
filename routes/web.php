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

Route::middleware(['auth', 'casemix.readonly', 'perawat.readonly'])->group(function () {

    Route::get('/rawat-inap', [RawatInapController::class, 'index'])->name('rawatinap.index');
    Route::get('/rawat-inap-data', [RawatInapController::class, 'data'])->name('rawatinap.data');
    Route::get('/rawat-inap/{id}', [RawatInapController::class, 'detail'])->name('rawatinap.detail');
    Route::post('/rawat-inap/{id}/update-pxrs', [RawatInapController::class, 'updatePxRS'])
        ->name('rawatinap.updatePxRS');
    Route::post('/rawatinap/{id}/simpan-kasir',[RawatInapController::class, 'simpanKasir'])->name('rawatinap.simpanKasir'); 
    Route::get('/cari-pasien-id', [RawatInapController::class, 'cariPasien'])->name('cari.pasien.id');       

        // Kwitansi Dan Rek
    Route::get('/rawatinap/{id}/kwitansi-print',[RawatInapController::class, 'kwitansiPrint'])
        ->name('rawatinap.kwitansiPrint');
    Route::get('/rawatinap/{id}/kwitansiphk3-print',[RawatInapController::class, 'kwitansiPhk3Print'])
        ->name('rawatinap.kwitansiPhk3Print');     
    Route::get('/rawat-inap/{id}/rekening-print', [RawatInapController::class, 'rekeningPrint'])
        ->name('rawatinap.rekeningPrint'); 
    Route::get('/rawat-inap/{id}/rek-rinci-print', [RawatInapController::class, 'rekRinciPrint'])
        ->name('rawatinap.rekRinciPrint');
    Route::get('/rawat-inap/{id}/rek-keu-print', [RawatInapController::class, 'rekKeuPrint'])
        ->name('rawatinap.rekKeuPrint');
    Route::get('/rawat-inap/{id}/rek-edit-obapay-print', [RawatInapController::class, 'rekEditObapayPrint'])
        ->name('rawatinap.rekEditObapayPrint');          
    Route::get('/rawat-inap/{id}/rek-edit-obapay-pdf', [RawatInapController::class, 'rekEditObapayPdf'])
        ->name('rawatinap.rekEditObapayPdf');          
              
        // Cek Sep dan BPJS
    Route::get('/sep/detail', [RawatInapController::class, 'sepDetail'])->name('sep.detail');
    Route::get('/bpjs/peserta', [RawatInapController::class, 'cekPesertaBpjs'])->name('bpjs.peserta');

        // Obat Dan ObaPay
    Route::get('/rawatinap/{id}/obat-rinci/{roomId}', [RawatInapController::class, 'obatRinciPrint']
        )->name('rawatinap.obatRinciPrint');
    Route::get('/rawatinap/{id}/obapay-print',[RawatInapController::class, 'obapayPrint']
        )->name('rawatinap.obapayPrint');
    Route::get('/rawatinap/{id}/obapay-edit-print', [RawatInapController::class, 'obapayEditPrint'])
        ->name('rawatinap.obapayEditPrint');   
      
        // Karcsi Jasa
    Route::post('/rawatinap/{id}/update-karcis-jasa',[RawatInapController::class, 'updateKarcisJasa'])
    ->name('rawatinap.updateKarcisJasa');
    Route::post('/rawatinap/{id}/hapus-karcis-jasa', [RawatInapController::class, 'hapusKarcisJasa'])
    ->name('rawatinap.hapusKarcisJasa');         

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

        // Visite
    Route::post('/rawatinap/visit/update', [RawatInapController::class, 'updateVisit'])
        ->name('rawatinap.updateVisit');
    Route::post('/rawatinap/visit/insert', [RawatInapController::class, 'insertVisit'])
        ->name('rawatinap.insertVisit');
    Route::post('/rawatinap/visit/delete', [RawatInapController::class, 'deleteVisit'])
        ->name('rawatinap.deleteVisit');        
        
        // Utilitas
    Route::post('/rawatinap/utilitas/insert', [RawatInapController::class, 'insertUtilitas'])
        ->name('rawatinap.insertUtilitas');    
    Route::post('/rawatinap/utilitas/update', [RawatInapController::class, 'updateUtilitas'])
        ->name('rawatinap.updateUtilitas');    
    Route::post('/rawatinap/utilitas/delete', [RawatInapController::class, 'deleteUtilitas'])
        ->name('rawatinap.deleteUtilitas');    

        // Biaya Lain
    Route::post('/rawatinap/insert-lain', [RawatInapController::class, 'insertLain'])
        ->name('rawatinap.insertLain');   
    Route::post('/rawatinap/update-lain', [RawatInapController::class, 'updateLain'])
        ->name('rawatinap.updateLain');
    Route::post('/rawatinap/delete-lain', [RawatInapController::class, 'deleteLain'])
        ->name('rawatinap.deleteLain');

        // Operasi
    Route::post('/rawatinap/operasi/insert', [RawatInapController::class, 'insertOperasi'])
        ->name('rawatinap.insertOperasi');    
    Route::post('/rawatinap/operasi/update', [RawatInapController::class, 'updateOperasi'])
        ->name('rawatinap.updateOperasi');    
    Route::post('/rawatinap/operasi/delete', [RawatInapController::class, 'deleteOperasi'])
        ->name('rawatinap.deleteOperasi'); 
    Route::get('/rawatinap/{id}/operasi/{ope_id}/print', [RawatInapController::class, 'operasiPrint'])
        ->name('rawatinap.operasiPrint');     
    
    // User Billing
    Route::get('/user-billing', [UserBillingController::class, 'index'])->name('userbilling.index');
    Route::post('/user-billing/store', [UserBillingController::class, 'store'])->name('userbilling.store');
    Route::put('/userbilling/{id}', [UserBillingController::class, 'update'])->name('userbilling.update');
    Route::delete('/userbilling/{id}', [UserBillingController::class, 'destroy'])->name('userbilling.destroy');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])
        ->name('profile.password.update');

        // Rawat Jalan
    Route::get('/rawatjalan', [RawatJalanController::class, 'index'])->name('rawatjalan.index');
    Route::get('/rawatjalan/data', [RawatJalanController::class, 'data'])->name('rawatjalan.data');
    Route::get('/rawatjalan/{id}', [RawatJalanController::class, 'detail'])->name('rawatjalan.detail');
    Route::get('/rawatjalan/{id}/rekening-print', [RawatJalanController::class, 'rekeningPrint'])
        ->name('rawatjalan.rekeningPrint');
    Route::post('/rawatjalan/{id}/update-karcis-jasa',[RawatJalanController::class, 'updateKarcisJasa'])
        ->name('rawatjalan.updateKarcisJasa');
    Route::post('/rawatjalan/{id}/hapus-karcis-jasa', [RawatJalanController::class, 'hapusKarcisJasa'])
        ->name('rawatjalan.hapusKarcisJasa');
    Route::post('/rawatjalan/{id}/auto-karcis-jasa', [RawatJalanController::class, 'autoKarcisJasa'])
        ->name('rawatjalan.autoKarcisJasa');            
        
       // IGD
    Route::get('/igd', [IGDController::class, 'index'])->name('igd.index');      
    Route::get('/igd/data', [IGDController::class, 'data'])->name('igd.data');  
    Route::get('/igd/{id}', [IGDController::class, 'detail'])->name('igd.detail');
    Route::get('/igd/{id}/rekening-print', [IGDController::class, 'rekeningPrint'])
        ->name('igd.rekeningPrint');
    Route::post('/igd/{id}/update-karcis-jasa',[IGDController::class, 'updateKarcisJasa'])
        ->name('igd.updateKarcisJasa');
    Route::post('/igd/{id}/hapus-karcis-jasa', [IGDController::class, 'hapusKarcisJasa'])
        ->name('igd.hapusKarcisJasa');
    Route::post('/igd/{id}/auto-karcis-jasa', [IGDController::class, 'autoKarcisJasa'])
        ->name('igd.autoKarcisJasa');            

        // Pasien Pulang
    Route::get('/pulang', [PulangController::class, 'index'])->name('pulang.index');
    Route::get('/pulang/data', [PulangController::class, 'data'])->name('pulang.data');
    Route::get('/pulang/{id}', [PulangController::class, 'detail'])->name('pulang.detail');
       
});

Route::middleware(['auth' ])->group(function () {
    // Dijamin PHK3
    Route::post('/rawatinap/{id}/update-dijamin-plafon', [RawatInapController::class, 'updateDijaminPlafon'])->name('rawatinap.updateDijaminPlafon');    
    Route::post('/rawatinap/{id}/hapus-dijamin-plafon', [RawatInapController::class, 'hapusDijaminPlafon'])
      ->name('rawatinap.hapusDijaminPlafon');   

    // Koding Awal
    Route::post('/rawatinap/{id}/koding-awal', [RawatInapController::class, 'simpanKodingAwal'])
        ->name('rawatinap.simpanKodingAwal');

});

Route::get('/home', function () {
    return redirect()->route('rawatinap.index');
})->name('home');