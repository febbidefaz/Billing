<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class RawatInapController extends Controller
{
    public function index()
    {
        return view('rawatinap');
    }

    public function data()
    {
        try {

            $data = DB::select("
                SET NOCOUNT ON;
                EXEC dbo.DaftarPasienRawatInap_SP
            ");

            return response()->json([
                'data' => $data ?: []
            ]);

        } catch (\Exception $e) {

            Log::error('RAWAT INAP DATA ERROR : ' . $e->getMessage());

            return response()->json([
                'data' => [],
                'message' => 'Data tidak ditemukan atau gagal dimuat.'
            ], 200);
        }
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

    // Insert PasInap
    public function insertPasInap(Request $request)
    {
        try {

            $request->validate([
                'ID' => 'required',
                'KelasID' => 'required',
                'RoomID' => 'required',
                'TMasuk' => 'required'
            ]);

            $result = DB::select("
                EXEC dbo.WebInsertPasInap_SP
                    @ID=?,
                    @KStrg=?,
                    @KelasID=?,
                    @RoomID=?,
                    @TMasuk=?,
                    @DokterID=?,
                    @Status=?,
                    @JMasuk=?,
                    @TKeluar=?,
                    @JKeluar=?,
                    @Pot=?,
                    @Biaya=?,
                    @Usr=?,
                    @Askep=?,
                    @Pot2=?,
                    @PotDay=?,
                    @kamar=?,
                    @japel=?,
                    @alat=?,
                    @posted=?,
                    @KodeBed=?
            ", [
                $request->ID,
                null, // KStrg
                $request->KelasID,
                $request->RoomID,
                $request->TMasuk,
                $request->DokterID,
                $request->Status ?? 'Dirawat',
                $request->JMasuk,
                $request->TKeluar,
                $request->JKeluar,
                $request->Pot ?? 0,
                str_replace(['Rp', '.', ' '], '', $request->Biaya ?? 0),
                session('username'),
                $request->Askep ?? 0,
                $request->Pot2 ?? 0,
                $request->PotDay ?? 0,
                str_replace(['Rp', '.', ' '], '', $request->Kamar ?? 0),
                str_replace(['Rp', '.', ' '], '', $request->Japel ?? 0),
                str_replace(['Rp', '.', ' '], '', $request->Alat ?? 0),
                0,
                $request->KodeBed
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Kamar rawat inap berhasil ditambahkan.',
                'nomer' => $result[0]->NomerBaru ?? null
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);

        }
    }

    // Del PasInap
    public function deletePasInap(Request $request)
    {
        try {

            $request->validate([
                'Nomer' => 'required|integer'
            ]);

            DB::statement(
                "EXEC dbo.WebDeletePasInapByNomer_SP ?",
                [$request->Nomer]
            );

            return response()->json([
                'success' => true,
                'message' => 'Kamar berhasil dihapus.'
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);

        }
    }

    // Update PasInap
    public function updatePasInap(Request $request)
    {
        $request->validate([
            'Nomer' => 'required|integer',
            'KelasID' => 'required|integer',
            'RoomID' => 'required|integer',
            'DokterID' => 'nullable|integer',
            'TMasuk' => 'nullable|date',
            'JMasuk' => 'nullable',
            'TKeluar' => 'nullable|date',
            'JKeluar' => 'nullable',
            'Status' => 'nullable|string|max:50',
            'Pot' => 'nullable|numeric',
            'PotDay' => 'nullable|integer',
            'Askep' => 'nullable|numeric',
            'Pot2' => 'nullable|numeric',
            'KodeBed' => 'nullable|string|max:50',
        ]);
    
        DB::statement("EXEC dbo.WebUpdatePasInapByNomer_SP ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?", [
            $request->Nomer,
            $request->KelasID,
            $request->RoomID,
            $request->DokterID,
            $request->TMasuk,
            $request->JMasuk,
            $request->TKeluar,
            $request->JKeluar,
            $request->Status,
            $request->Pot ?? 0,
            $request->PotDay ?? 0,
            $request->Askep ?? 0,
            $request->Pot2 ?? 0,
            $request->KodeBed,
        ]);
    
        return response()->json([
            'success' => true
        ]);
    }

    // Insert biaya lain
    public function insertLain(Request $request)
    {
        try {
            $request->validate([
                'ID'        => 'required|integer',
                'TGL'       => 'nullable|date',
                'Lain'      => 'required|string|max:50',
                'BiayaLain' => 'required|numeric',
                'Pot'       => 'nullable|numeric',
            ]);

            $tgl = $request->TGL
                ? Carbon::parse($request->TGL, 'Asia/Jakarta')
                    ->setTimeFrom(Carbon::now('Asia/Jakarta'))
                    ->format('Y-m-d H:i:s')
                : Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s');

            $result = DB::select("
                EXEC dbo.WebInsertLainByID_SP
                    @ID = ?,
                    @TGL = ?,
                    @Lain = ?,
                    @BiayaLain = ?,
                    @Pot = ?
            ", [
                $request->ID,
                $tgl,
                $request->Lain,
                $request->BiayaLain ?? 0,
                ($request->Pot ?? 0) / 100,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Biaya lain-lain berhasil ditambahkan.',
                'lain_id' => $result[0]->Lain_ID_Baru ?? null,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // Update Biaya Lain    
    public function updateLain(Request $request)
    {
        $request->validate([
            'ID' => 'required|integer',
            'Lain_ID' => 'required|integer',
            'TGL' => 'nullable|date',
            'Lain' => 'required|string|max:50',
            'BiayaLain' => 'required|numeric',
            'Pot' => 'nullable|numeric',
        ]);

        $tgl = $request->TGL
            ? Carbon::parse($request->TGL, 'Asia/Jakarta')
                ->setTimeFrom(Carbon::now('Asia/Jakarta'))
                ->format('Y-m-d H:i:s')
            : Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s');

        DB::statement("
            EXEC dbo.WebUpdateLainByID_SP ?, ?, ?, ?, ?, ?
        ", [
            $request->ID,
            $request->Lain_ID,
            $tgl,
            $request->Lain,
            $request->BiayaLain,
            ($request->Pot ?? 0) / 100,
        ]);

        return response()->json(['success' => true]);
    }

    // Del Biaya Lain
    public function deleteLain(Request $request)
    {
        $request->validate([
            'ID' => 'required|integer',
            'Lain_ID' => 'required|integer',
        ]);

        DB::statement("EXEC dbo.WebDeleteLainByID_SP ?, ?", [
            $request->ID,
            $request->Lain_ID,
        ]);

        return response()->json(['success' => true]);
    }

    // Obat All
    public function obatRinciPrint($id, $roomId)
    {
        $obatRinci = DB::select("EXEC dbo.ObatPxRI_ALL_SP ?, ?", [
            $id,
            $roomId
        ]);

        $pasien = $obatRinci[0] ?? null;

        return view('rawatinap.obat-rinci-print', compact(
            'obatRinci',
            'pasien',
            'roomId'
        ));
    }

    // ObaPay All
    public function obapayPrint($id)
    {
        $salesFarmasi = [];
        $grandTotalFarmasiApi = 0;

        try {

            $token = $this->getFarmasiToken();

            $response = Http::withToken($token)
                ->timeout(15)
                ->get('http://192.168.1.9:8010/api/sales', [
                    'appointment_id' => $id
                ]);

            if ($response->successful()) {

                $salesFarmasi = $response->json('data.sales') ?? [];
                $grandTotalFarmasiApi = $response->json('data.grand_total') ?? 0;
            }

        } catch (\Exception $e) {

            Log::error($e->getMessage());
        }

        return view('rawatinap.obapay-print', compact(
            'id',
            'salesFarmasi',
            'grandTotalFarmasiApi'
        ));
    }

    // Simpan Otoritas Kasir
    public function simpanKasir(Request $request, $id)
    {
        $request->validate([
            'KasirID' => 'required|integer',
            'payBy'   => 'nullable|string|max:60',
            'Shift'   => 'nullable|string|max:2',
        ]);
    
        try {
            DB::statement("EXEC dbo.WebInsertKasirByID_SP ?, ?, ?, ?", [
                $id,
                $request->KasirID,
                $request->payBy,
                $request->Shift
            ]);
    
            return back()->with('success', 'Data kasir berhasil disimpan.');
    
        } catch (\Exception $e) {
            return back()->with('error', 'Data kasir sudah ada dan tidak dapat diubah.');
        }
    }

    // Update Tanggal Pulang
    public function updateTglBayar(Request $request, $id)
    {
        $request->validate([
            'TglByr' => 'required|date'
        ]);

        DB::statement(
            "EXEC dbo.WebUpdateTglBayarByID_SP ?, ?",
            [
                $id,
                $request->TglByr
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Tanggal bayar berhasil disimpan'
        ]);
    }

    // Hapus Tanggl Pulang
    public function hapusTglBayar($id)
    {
        DB::statement(
            "EXEC dbo.WebHapusTglBayarByID_SP ?",
            [$id]
        );

        return response()->json([
            'success' => true
        ]);
    }

    // Terbilang
    private function terbilang($angka)
    {
        $angka = abs($angka);

        $baca = [
            '',
            'satu',
            'dua',
            'tiga',
            'empat',
            'lima',
            'enam',
            'tujuh',
            'delapan',
            'sembilan',
            'sepuluh',
            'sebelas',
        ];

        if ($angka < 12) {
            return $baca[$angka];
        } elseif ($angka < 20) {
            return $this->terbilang($angka - 10) . ' belas';
        } elseif ($angka < 100) {
            return $this->terbilang(floor($angka / 10)) . ' puluh ' .
                $this->terbilang($angka % 10);
        } elseif ($angka < 200) {
            return 'seratus ' . $this->terbilang($angka - 100);
        } elseif ($angka < 1000) {
            return $this->terbilang(floor($angka / 100)) . ' ratus ' .
                $this->terbilang($angka % 100);
        } elseif ($angka < 2000) {
            return 'seribu ' . $this->terbilang($angka - 1000);
        } elseif ($angka < 1000000) {
            return $this->terbilang(floor($angka / 1000)) . ' ribu ' .
                $this->terbilang($angka % 1000);
        } elseif ($angka < 1000000000) {
            return $this->terbilang(floor($angka / 1000000)) . ' juta ' .
                $this->terbilang($angka % 1000000);
        }

        return '';
    }

    // Kwitansi
    public function kwitansiPrint($id)
{
    $pasien = DB::selectOne("EXEC dbo.WebPasienRawatInapDetailByID_SP ?", [$id]);

    $kamar = DB::select("EXEC dbo.WebKamarBillingByID_SP ?", [$id]);
    $rekeningVisit = DB::select("EXEC dbo.WebRekeningVisitByID_SP ?", [$id]);
    $rekeningUtilitas = DB::select("EXEC dbo.WebRekeningUtilitasByID_SP ?", [$id]);
    $rekeningLaborat = DB::select("EXEC dbo.WebRekeningLaboratByID_SP ?", [$id]);
    $rekeningRadiologi = DB::select("EXEC dbo.WebRekeningRadiologiByID_SP ?", [$id]);
    $lainlain = DB::select("EXEC dbo.WebLainBillingByID_SP ?", [$id]);
    $rekeningOperasi = DB::select("EXEC dbo.WebRekeningOperasiByID_SP ?", [$id]);
    $obat = DB::select("EXEC dbo.WebObatBillingByID_SP ?", [$id]);
    $kasir = DB::selectOne("EXEC dbo.WebKasirBillingByID_SP ?", [$id]);

    $karcisJasa = ($pasien->Biaya ?? 0) + ($pasien->JasaPrk ?? 0);
    $totalLab = collect($rekeningLaborat)->sum('Netto');
    $totalRadiologi = collect($rekeningRadiologi)->sum('Netto');
    $totalVisitRuang = collect($rekeningVisit)->sum('Netto') + collect($kamar)->sum('TotalSewa') + collect($kamar)->sum('TotalAskep');
    $totalUtilitas = collect($rekeningUtilitas)->sum('Netto');
    $totalOperasi = collect($rekeningOperasi)->sum('Netto');
    $totalObat = collect($obat)->sum('HutangObat');
    $totalLain = collect($lainlain)->sum('TotalLain');

    $salesFarmasi = [];
    $grandTotalFarmasiApi = 0;

    try {

        $token = $this->getFarmasiToken();

        $response = Http::withToken($token)
            ->timeout(15)
            ->get('http://192.168.1.9:8010/api/sales', [
                'appointment_id' => $id
            ]);

        if ($response->successful()) {
            $grandTotalFarmasiApi = $response->json('data.grand_total') ?? 0;
        }

    } catch (\Exception $e) {
        $grandTotalFarmasiApi = 0;
    }
    $totalObat = collect($obat)->sum('HutangObat') + $grandTotalFarmasiApi;

    $grandTotal = $karcisJasa + $totalLab + $totalRadiologi + $totalVisitRuang + $totalUtilitas + $totalOperasi + $totalObat + $totalLain;
    $dijamin = $pasien->DownPay ?? 0;
    $sisa = $grandTotal - $dijamin;

    $terbilangSisa = strtoupper(trim(preg_replace('/\s+/', ' ', $this->terbilang($sisa))));
    $tanggalCetak = now()->translatedFormat('l, d F Y');

    return view('rawatinap.kwitansi-print', compact(
        'pasien',
        'karcisJasa',
        'totalLab',
        'totalRadiologi',
        'totalVisitRuang',
        'totalUtilitas',
        'totalOperasi',
        'totalObat',
        'totalLain',
        'grandTotal',
        'dijamin',
        'sisa',
        'terbilangSisa',
        'tanggalCetak',
        'kasir'
    ));
}

    // Rekening print
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

        // Oba Pay
        $salesFarmasi = [];
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
                $salesFarmasi = $response->json('data.sales') ?? [];
                $grandTotalFarmasiApi = $response->json('data.grand_total') ?? 0;
            }
    
        } catch (\Exception $e) {
            Log::error('Farmasi API Rekening Print Error : ' . $e->getMessage());
    
            $salesFarmasi = [];
            $grandTotalFarmasiApi = 0;
        }

        $totalKamar = collect($kamar)->sum('TotalSewa');
        $totalAskep = collect($kamar)->sum('TotalAskep');
        $totalVisit = collect($rekeningVisit)->sum('Netto');
        $totalUtilitas = collect($rekeningUtilitas)->sum('Netto');
        $totalLain = collect($lainlain)->sum('TotalLain');
        $totalOperasi = collect($rekeningOperasi)->sum('Netto');
        $totalObat = collect($obat)->sum('HutangObat');

        $karcisJasa = ($pasien->Biaya ?? 0) + ($pasien->JasaPrk ?? 0);

        $grandTotal =
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

        $dijamin = $pasien->DownPay ?? 0;
        $sisa = $grandTotal - $dijamin;

        $terbilangSisa = strtoupper(
            trim(
                preg_replace('/\s+/', ' ', $this->terbilang($sisa))
            )
        );

        $tanggalCetak = now()->format('d M Y H:i:s');
    
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
            'obat',
            'salesFarmasi',
            'grandTotalFarmasiApi',
            'sisa',
            'terbilangSisa',
            'tanggalCetak'
        ));
    }

    //Ambil Token ObaPay
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

    public function detail($id) 
    {
    // pasien
    $pasien = DB::selectOne("EXEC dbo.WebPasienRawatInapDetailByID_SP ?", [$id]);

    //kamar
    $kamar = DB::select("EXEC dbo.WebKamarBillingByID_SP ?", [$id]);
    //Pilih Kamar
    $kamarList = DB::select("EXEC dbo.cboKelasStrg_sp");
    // Staus PAsInap
    $pxStateList = DB::select("EXEC dbo.cboPxState_SP"); 

    // Dokter
    $dokterList = DB::select("EXEC dbo.cboDokter_SP");
 
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

    // Obat Billing
    $obat = DB::select("EXEC dbo.WebObatBillingByID_SP ?", [$id]);

     // ObaPay
     $salesFarmasi = [];
     $grandTotalFarmasiApi = 0;
 
     try {

        $token = $this->getFarmasiToken();
    
        $response = Http::withToken($token)
            ->timeout(15)
            ->get('http://192.168.1.9:8010/api/sales', [
                'appointment_id' => $id
            ]);
    
        if ($response->successful()) {
            $salesFarmasi = $response->json('data.sales') ?? [];
            $grandTotalFarmasiApi = $response->json('data.grand_total') ?? 0;
        }
    
    } catch (\Exception $e) {
    
        Log::error('Farmasi API Error : ' . $e->getMessage());
    
        $salesFarmasi = [];
        $grandTotalFarmasiApi = 0;
    }

     //Get Upx
    $upxList = DB::select("EXEC dbo.cboUpx_sp");
    //Room Obat
    $roomObatList = DB::select("EXEC dbo.cboRoom_SP");
    //Otoritas    
    $kasir = DB::select("EXEC dbo.WebKasirBillingByID_SP ?",[$id]);
    //kasirRS   
    $kasirList = DB::select("EXEC dbo.cboKasirRS_SP");

    return view('rawatinap.inapdetail', compact(
        'pasien', 
        'dokterList',
        'kamar', 
        'kamarList',
        'pxStateList',
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
        'salesFarmasi',
        'grandTotalFarmasiApi',
        'upxList',
        'roomObatList',
        'kasir',
        'kasirList',));
        }
    }
    

