<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class MonitoringController extends Controller
{
    public function index()
    {
        return view('monitoring.ri');
    }

    public function data1()
    {
        $data = DB::select('EXEC WebRIMonitoring_SP');

        return response()->json([
            'data' => $data
        ]);
    }

    public function data()
    {
        $data = DB::select('EXEC WebRIMonitoring_SP');

        $data = collect($data)->map(function ($row) {

            $obapay = $this->getTotalObapay($row->ID);

            $row->Obapay = $obapay;
            $row->Total = ($row->Total ?? 0) + $obapay;

            return $row;
        })->values();

        return response()->json([
            'data' => $data
        ]);
    }

    private function getTotalObapay($id)
    {
        try {
            $token = $this->getFarmasiToken();

            $response = Http::withToken($token)
                ->timeout(15)
                ->get('http://192.168.1.9:8010/api/sales', [
                    'appointment_id' => $id
                ]);

            if ($response->status() == 401) {
                Cache::forget('farmasi_token');

                $token = $this->getFarmasiToken();

                $response = Http::withToken($token)
                    ->timeout(3)
                    ->get('http://192.168.1.9:8010/api/sales', [
                        'appointment_id' => $id
                    ]);
            }

            if ($response->successful()) {
                return $response->json('data.grand_total') ?? 0;
            }

            return 0;

        } catch (\Exception $e) {
            Log::error('Obapay Monitoring Error : ' . $e->getMessage());
            return 0;
        }
    }

    private function hitungTotalBiayaRI($id)
    {
        $pasien = DB::selectOne("EXEC dbo.WebPasienRawatInapDetailByID_SP ?", [$id]);
    
        $kamar              = DB::select("EXEC dbo.WebKamarBillingByID_SP ?", [$id]);
        $rekeningVisit      = DB::select("EXEC dbo.WebRekeningVisitByID_SP ?", [$id]);
        $rekeningUtilitas   = DB::select("EXEC dbo.WebRekeningUtilitasByID_SP ?", [$id]);
        $rekeningLaborat    = DB::select("EXEC dbo.WebRekeningLaboratByID_SP ?", [$id]);
        $rekeningRadiologi  = DB::select("EXEC dbo.WebRekeningRadiologiByID_SP ?", [$id]);
        $lainlain           = DB::select("EXEC dbo.WebLainBillingByID_SP ?", [$id]);
        $rekeningOperasi    = DB::select("EXEC dbo.WebRekeningOperasiByID_SP ?", [$id]);
        $obat               = DB::select("EXEC dbo.WebObatBillingByID_SP ?", [$id]);
    
        // ObaPay
        $grandTotalFarmasiApi = 0;
    
        try {
            $token = $this->getFarmasiToken();
    
            $response = Http::withToken($token)
                ->timeout(15)
                ->get('http://192.168.1.9:8010/api/sales', [
                    'appointment_id' => $id
                ]);
    
            if ($response->status() == 401) {
                Cache::forget('farmasi_token');
    
                $token = $this->getFarmasiToken();
    
                $response = Http::withToken($token)
                    ->timeout(15)
                    ->get('http://192.168.1.9:8010/api/sales', [
                        'appointment_id' => $id
                    ]);
            }
    
            if ($response->successful()) {
                $grandTotalFarmasiApi = $response->json('data.grand_total') ?? 0;
            }
    
        } catch (\Exception $e) {
            Log::error('Farmasi API Monitoring RI Error : ' . $e->getMessage());
            $grandTotalFarmasiApi = 0;
        }
    
        $karcisJasa     = ($pasien->Biaya ?? 0) + ($pasien->JasaPrk ?? 0);
        $totalKamar     = collect($kamar)->sum('TotalSewa');
        $totalAskep     = collect($kamar)->sum('TotalAskep');
        $totalVisit     = collect($rekeningVisit)->sum('Netto');
        $totalUtilitas  = collect($rekeningUtilitas)->sum('Netto');
        $totalLab       = collect($rekeningLaborat)->sum('Netto');
        $totalRadiologi = collect($rekeningRadiologi)->sum('Netto');
        $totalLain      = collect($lainlain)->sum('TotalLain');
        $totalOperasi   = collect($rekeningOperasi)->sum('Netto');
        $totalObat      = collect($obat)->sum('HutangObat');
    
        return
            $karcisJasa +
            $totalKamar +
            $totalAskep +
            $totalVisit +
            $totalUtilitas +
            $totalRadiologi +
            $totalLab +
            $totalLain +
            $totalOperasi +
            $totalObat +
            $grandTotalFarmasiApi;
    }

    private function getFarmasiToken()
    {
        return Cache::remember('farmasi_token', 360, function () {
            $response = Http::post('http://192.168.1.9:8010/api/token', [
                'username' => env('FARMASI_USER'),
                'password' => env('FARMASI_PASS')
            ]);

            if (!$response->successful()) {
                throw new \Exception('Gagal mendapatkan token');
            }

            return $response->json('token');
        });
    }

    public function rinci($id)
    {
        $pasien = DB::selectOne("EXEC dbo.WebPasienRawatInapDetailByID_SP ?", [$id]);
    
        $kamar              = DB::select("EXEC dbo.WebKamarBillingByID_SP ?", [$id]);
        $rekeningVisit      = DB::select("EXEC dbo.WebRekeningVisitByID_SP ?", [$id]);
        $rekeningUtilitas   = DB::select("EXEC dbo.WebRekeningUtilitasByID_SP ?", [$id]);
        $rekeningLaborat    = DB::select("EXEC dbo.WebRekeningLaboratByID_SP ?", [$id]);
        $rekeningRadiologi  = DB::select("EXEC dbo.WebRekeningRadiologiByID_SP ?", [$id]);
        $lainlain           = DB::select("EXEC dbo.WebLainBillingByID_SP ?", [$id]);
        $rekeningOperasi    = DB::select("EXEC dbo.WebRekeningOperasiByID_SP ?", [$id]);
        $obat               = DB::select("EXEC dbo.WebObatBillingByID_SP ?", [$id]);
    
        $karcisJasa     = ($pasien->Biaya ?? 0) + ($pasien->JasaPrk ?? 0);
        $totalKamar     = collect($kamar)->sum('TotalSewa');
        $totalVisit     = collect($rekeningVisit)->sum('Netto');
        $totalUtilitas  = collect($rekeningUtilitas)->sum('Netto');
        $totalLab       = collect($rekeningLaborat)->sum('Netto');
        $totalRadiologi = collect($rekeningRadiologi)->sum('Netto');
        $totalLain      = collect($lainlain)->sum('TotalLain');
        $totalOperasi   = collect($rekeningOperasi)->sum('Netto');
        $totalObat      = collect($obat)->sum('HutangObat');     
        $obapay = $this->getTotalObapay($id);
        $totalObat = collect($obat)->sum('HutangObat') + $obapay;

    
        return response()->json([
            'karcisJasa' => $karcisJasa,
            'kamar'      => $totalKamar,
            'visit'      => $totalVisit,
            'utilitas'   => $totalUtilitas,
            'lab'        => $totalLab,
            'radiologi'  => $totalRadiologi,
            'lainlain'   => $totalLain,
            'operasi'    => $totalOperasi,
            'obat'       => $totalObat
        ]);
    }
}