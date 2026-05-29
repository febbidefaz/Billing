<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class RawatInapController extends Controller
{
    public function index()
    {
        return view('rawatinap');
    }

    public function sepDetail(Request $request)
    {
        $nosep = $request->query('nosep');

        $response = Http::timeout(10)->get('http://192.168.1.200:6000/api/findsep', [
            'nosep' => $nosep
        ]);

        return response()->json($response->json());
    }

    public function cekPesertaBpjs(Request $request)
    {
        $noKartu = $request->query('noKartu');

        $response = Http::timeout(10)->get('http://192.168.1.200:6000/api/peserta/nokartu', [
            'noKartu' => $noKartu
        ]);

        return response()->json($response->json());
    }

    public function data()
    {
        $data = DB::select("EXEC dbo.DaftarPasienRawatInap_SP");

        return response()->json([
            'data' => $data
        ]);
    }   

    public function updatePxRS(Request $request, $id)
    {
        $request->validate([
            'uPx' => 'required|integer'
        ]);
    
        DB::statement("
            SET NOCOUNT ON;
            EXEC dbo.WebUpdatePxRSByID_SP ?, ?
        ", [
            $id,
            $request->input('uPx')
        ]);
    
        return response()->noContent();
    }

    public function rekeningPrint($id)
    {
        $pasien = DB::selectOne("EXEC dbo.WebPasienRawatInapDetailByID_SP ?", [$id]);
    
        $kamar = DB::select("EXEC dbo.WebKamarBillingByID_SP ?", [$id]);
        $rekeningVisit = DB::select("EXEC dbo.WebRekeningVisitByID_SP ?", [$id]);
        $rekeningUtilitas = DB::select("EXEC dbo.WebRekeningUtilitasByID_SP ?", [$id]);
        $rekeningLaborat = DB::select("EXEC dbo.WebRekeningLaboratByID_SP ?", [$id]);        
        $totalLab = collect($rekeningLaborat)->sum('Netto');

        $rekeningRadiologi = DB::select("EXEC dbo.WebRekeningRadiologiByID_SP ?", [$id]);
        $totalRadiologi = collect($rekeningRadiologi)->sum('Netto');

        $lainlain = DB::select("EXEC dbo.WebLainBillingByID_SP ?", [$id]);
        $rekeningOperasi = DB::select("EXEC dbo.WebRekeningOperasiByID_SP ?", [$id]);
        $obat = DB::select("EXEC dbo.WebObatBillingByID_SP ?", [$id]);

  
    
        return view('rawatinap.rekening-print', compact(
            'pasien',
            'kamar',
            'rekeningVisit',
            'rekeningUtilitas',
            'rekeningLaborat',
            'totalLab',
            'rekeningRadiologi',
            'totalRadiologi',
            'lainlain',
            'rekeningOperasi',
            'obat'
      
        ));
    }

    public function detail($id) 
    {
    // pasien
    $pasien = DB::selectOne("EXEC dbo.WebPasienRawatInapDetailByID_SP ?", [$id]);

    //kamar
    $kamar = DB::select("EXEC dbo.WebKamarBillingByID_SP ?", [$id]);

    // Visite 
    $visitdokter = DB::select("EXEC dbo.WebVisitBillingByID_SP ?", [$id]);

    // Radiologi
    $radiologi = DB::select("EXEC dbo.WebRadiologiBillingByID_SP ?", [$id]);

    $radiologiDetail = [];

    foreach ($radiologi as $r) {
        $radiologiDetail[$r->IDRad] = DB::select(
            "EXEC dbo.WebRadiologiDetailByIDRad_SP ?",
            [$r->IDRad]
        );
    }

    $radiologiDetailFlat = collect($radiologiDetail)->flatten(1);


    // Laborat
    $lab = DB::select("EXEC dbo.WeblaboratByIDReg_SP ?", [$id]);

    $labDetail = [];

    foreach ($lab as $l) {
        $labDetail[$l->IDLab] = DB::select(
            "EXEC dbo.WebLaboratDetailByIDLab_SP ?",
            [$l->IDLab]
        );
    }

    $labDetailFlat = collect($labDetail)->flatten(1);

    // Utilitas / Tindakan Dokter
    $utilitas = DB::select("EXEC dbo.WebUtilitasBillingByID_SP ?", [$id]);

    // Lain - lain
    $lainlain = DB::select("EXEC dbo.WebLainBillingByID_SP ?", [$id]);

    // Operasi
    $operasi = DB::select("EXEC dbo.WebOperasiBillingByID_SP ?", [$id]);

    $obat = DB::select("EXEC dbo.WebObatBillingByID_SP ?", [$id]);
    $upxList = DB::select("EXEC dbo.cboUpx_sp");

    return view('rawatinap.inapdetail', compact(
        'pasien', 
        'kamar', 
        'visitdokter', 
        'utilitas',
        'radiologi',
        'radiologiDetail',
        'radiologiDetailFlat', 
        'lab',
        'labDetail',
        'labDetailFlat',
        'lainlain', 
        'operasi', 
        'obat', 
        'radiologi', 
        'radiologiDetailFlat',
        'upxList'));
        }
    }

