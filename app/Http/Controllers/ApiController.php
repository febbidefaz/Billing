<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ApiController extends Controller
{
    public function getToken(Request $request)
    {
        try {

            $request->validate([
                'username' => 'required|string',
                'password' => 'required|string',
            ]);

            $user = DB::table('UserToken')
                ->where('Username', $request->username)
                ->where('Aktif', 1)
                ->first();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Username tidak ditemukan atau tidak aktif.'
                ], 401);
            }

            if (!Hash::check($request->password, $user->Password)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Password salah.'
                ], 401);
            }

            // Token berlaku 10 menit      
            $expiredAt = now('Asia/Jakarta')->addMinutes(10);

            $payload = [
                'id' => $user->ID,
                'username' => $user->Username,
                'expired_at' => $expiredAt->timestamp
            ];

            // Token tidak disimpan di database
            $token = Crypt::encryptString(
                json_encode($payload)
            );

            return response()->json([
                'status' => true,
                'message' => 'Token berhasil dibuat.',
                'token_type' => 'Bearer',
                'token' => $token,
                'expires_in' => 600,
                'expires_at' => $expiredAt->format('Y-m-d H:i:s')
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {

            return response()->json([
                'status' => false,
                'message' => $e->validator->errors()->first()
            ], 422);

        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => 'Gagal membuat token.'
            ], 500);
        }
    }

    public function pasienPulang(Request $request)
    {
        try {

            $request->validate(
                [
                    'tgl_awal'  => 'nullable|date',
                    'tgl_akhir' => 'nullable|date|after_or_equal:tgl_awal',
                ],
                [
                    'tgl_awal.date' => 'Tanggal awal tidak valid.',
                    'tgl_akhir.date' => 'Tanggal akhir tidak valid.',
                    'tgl_akhir.after_or_equal' => 'Tanggal akhir harus sama dengan atau lebih besar dari tanggal awal.'
                ]
            );

            $tglAwal  = $request->tgl_awal ?? date('Y-m-d');
            $tglAkhir = $request->tgl_akhir ?? date('Y-m-d');

            $data = DB::select("
                SET NOCOUNT ON;
                EXEC dbo.WebDaftarPasienPulang_SP ?, ?
            ", [
                $tglAwal,
                $tglAkhir
            ]);

            return response()->json([
                'status' => true,
                'tgl_awal' => $tglAwal,
                'tgl_akhir' => $tglAkhir,
                'jumlah' => count($data),
                'data' => $data ?: []
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {

            return response()->json([
                'status' => false,
                'message' => $e->validator->errors()->first(),
                'data' => []
            ], 422);

        } catch (\Exception $e) {

            Log::error('API PASIEN PULANG ERROR : ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Data gagal dimuat.',
                'data' => []
            ], 500);
        }
    }

    public function igd(Request $request)
    {
        try {
            $request->validate(
                [
                    'tgl_awal'  => 'nullable|date',
                    'tgl_akhir' => 'nullable|date|after_or_equal:tgl_awal',
                ],
                [
                    'tgl_awal.date' => 'Tanggal awal tidak valid.',
                    'tgl_akhir.date' => 'Tanggal akhir tidak valid.',
                    'tgl_akhir.after_or_equal' => 'Tanggal akhir harus sama dengan atau lebih besar dari tanggal awal.'
                ]
            );
    
            $tglAwal  = $request->tgl_awal ?? date('Y-m-d');
            $tglAkhir = $request->tgl_akhir ?? date('Y-m-d');
    
            $data = DB::select("
                SET NOCOUNT ON;
                EXEC dbo.WebDaftarPasienRawatJalanIGD_SP ?, ?
            ", [
                $tglAwal,
                $tglAkhir
            ]);
    
            return response()->json([
                'status' => true,
                'tgl_awal' => $tglAwal,
                'tgl_akhir' => $tglAkhir,
                'jumlah' => count($data),
                'data' => $data ?: []
            ], 200);
    
        } catch (\Illuminate\Validation\ValidationException $e) {
    
            return response()->json([
                'status' => false,
                'message' => $e->validator->errors()->first(),
                'data' => []
            ], 422);
    
        } catch (\Exception $e) {
    
            Log::error('IGD DATA ERROR : ' . $e->getMessage());
    
            return response()->json([
                'status' => false,
                'message' => 'Data tidak ditemukan atau gagal dimuat.',
                'data' => []
            ], 500);
        }
    }

    public function rawatJalan(Request $request)
    {
        try {
            $request->validate(
                [
                    'tgl_awal'     => 'nullable|date',
                    'tgl_akhir'    => 'nullable|date|after_or_equal:tgl_awal',
                    'id_spesialis' => 'nullable|integer',
                    'dokter_id'    => 'nullable|integer',
                ],
                [
                    'tgl_awal.date' => 'Tanggal awal tidak valid.',
                    'tgl_akhir.date' => 'Tanggal akhir tidak valid.',
                    'tgl_akhir.after_or_equal' =>
                        'Tanggal akhir harus sama dengan atau lebih besar dari tanggal awal.',

                    'id_spesialis.integer' =>
                        'ID spesialis harus berupa angka.',

                    'dokter_id.integer' =>
                        'ID dokter harus berupa angka.',
                ]
            );

            $tglAwal  = $request->tgl_awal ?? date('Y-m-d');
            $tglAkhir = $request->tgl_akhir ?? date('Y-m-d');

            $idSpesialis = $request->id_spesialis;
            $dokterId    = $request->dokter_id;

            $data = DB::select("
                SET NOCOUNT ON;
                EXEC dbo.WebApiDaftarPasienRawatJalan_SP ?, ?, ?, ?
            ", [
                $tglAwal,
                $tglAkhir,
                $idSpesialis,
                $dokterId
            ]);

            return response()->json([
                'status' => true,

                'filter' => [
                    'tgl_awal' => $tglAwal,
                    'tgl_akhir' => $tglAkhir,
                    'id_spesialis' => $idSpesialis,
                    'dokter_id' => $dokterId
                ],

                'jumlah' => count($data),

                'data' => $data ?: []

            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {

            return response()->json([
                'status' => false,
                'message' => $e->validator->errors()->first(),
                'data' => []
            ], 422);

        } catch (\Exception $e) {

            Log::error('RAWAT JALAN DATA ERROR : ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Data tidak ditemukan atau gagal dimuat.',
                'data' => []
            ], 500);
        }
    }

    public function spesialis()
    {
        try {

            $data = DB::select("
                SET NOCOUNT ON;
                EXEC dbo.cboSpesialis_SP
            ");

            return response()->json([
                'status' => true,
                'jumlah' => count($data),
                'data' => $data ?: []
            ], 200);

        } catch (\Exception $e) {

            Log::error('SPESIALIS DATA ERROR : ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Data spesialis tidak ditemukan atau gagal dimuat.',
                'data' => []
            ], 500);
        }
    }

    public function dokter()
    {
        try {

            $data = DB::select("
                SELECT 
                    ID,
                    DokterAlias As Dokter
                FROM Dokter
                WHERE Aktif = 1
                ORDER BY NoUrut, DokterAlias
            ");

            return response()->json([
                'status' => true,
                'jumlah' => count($data),
                'data' => $data ?: []
            ], 200);

        } catch (\Exception $e) {

            Log::error('DOKTER DATA ERROR : ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Data dokter tidak ditemukan atau gagal dimuat.',
                'data' => []
            ], 500);
        }
    }

    public function akunAll(Request $request)
    {
        try {
            $request->validate(
                [
                    'id' => 'required|integer',
                ],
                [
                    'id.required' => 'ID harus diisi.',
                    'id.integer'  => 'ID harus berupa angka.',
                ]
            );
    
            $id = $request->id;
    
            $data = DB::select("
                SELECT
                    ID,
                    biaya,
                    akun,
                    jml,
                    job,
                    IDReg
                FROM AkunALL
                WHERE IDReg = ?
            ", [
                $id
            ]);
    
            $nama = null;
    
            if (!empty($data)) {
                $pecah = explode('/', $data[0]->ID, 2);
                $nama = isset($pecah[1]) ? trim($pecah[1]) : null;
            }
    
            $hasil = collect($data)->map(function ($item) {
                return [
                    'biaya' => (int) $item->biaya,
                    'akun'  => $item->akun,
                    'jml'   => (int) $item->jml,
                    'job'   => $item->job,
                ];
            })->values();
    
            return response()->json([
                'status' => true,
                'IDReg'  => $id,
                'Nama'   => $nama,
                'jumlah' => $hasil->count(),
                'data'   => $hasil
            ], 200);
    
        } catch (\Illuminate\Validation\ValidationException $e) {
    
            return response()->json([
                'status' => false,
                'message' => $e->validator->errors()->first(),
                'data' => []
            ], 422);
    
        } catch (\Exception $e) {
    
            Log::error('AKUN ALL DATA ERROR : ' . $e->getMessage());
    
            return response()->json([
                'status' => false,
                'message' => 'Data tidak ditemukan atau gagal dimuat.',
                'data' => []
            ], 500);
        }
    }
}
