<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ObapayEditController extends Controller
{
    public function sync($id)
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
                    ->timeout(15)
                    ->get('http://192.168.1.9:8010/api/sales', [
                        'appointment_id' => $id
                    ]);
            }

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengambil data ObaPay.'
                ], 500);
            }

            $sales = $response->json('data.sales') ?? [];

            DB::beginTransaction();

            try {
            
                // Hapus seluruh data ObaPay pasien ini
                DB::table('dbo.WebObapayEdit')
                    ->where('ID', $id)
                    ->delete();
            
                // Insert ulang dari API ObaPay
                foreach ($sales as $sale) {
            
                    $items = $sale['items'] ?? [];
            
                    foreach ($items as $item) {
            
                        DB::table('dbo.WebObapayEdit')->insert([
                            'ID'        => $id,
                            'SaleID'    => $sale['transaction_no'] ?? null,
                            'Tanggal'   => $sale['date'] ?? null,
                            'NamaItem'  => $item['name'] ?? null,
                            'Code'      => $item['code'] ?? null,
                            'Unit'      => $item['unit'] ?? null,
                            'Qty'       => $item['qty'] ?? 0,
                            'Harga'     => $item['unit_price'] ?? 0,
                            'Total'     => $item['subtotal'] ?? 0,
                            'TotalEdit' => $item['subtotal'] ?? 0,
                            'IsEdited'  => 0,
                            'CreatedAt' => DB::raw('GETDATE()'),
                        ]);
            
                    }
                }
            
                DB::commit();
            
            } catch (\Exception $e) {
            
                DB::rollBack();
                throw $e;
            
            }
            return response()->json([
                'success' => true,
                'message' => 'Data ObaPay berhasil disinkronkan.'
            ]);

        } catch (\Exception $e) {
            Log::error('ERROR sync ObaPay', [
                'id' => $id,
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request)
    {
        if (!in_array(strtolower(Auth::user()->Role), ['kasir', 'casemix', 'perawat'])) {
            abort(403, 'Anda tidak memiliki hak mengubah ObaPay.');
        }

        $request->validate([
            'IDObapay' => 'required|integer',
            'NamaItem' => 'required|string',
            'Code'     => 'nullable|string|max:100',
            'Unit'     => 'nullable|string|max:50',
            'Qty'      => 'required|integer|min:0',
            'Harga'    => 'required|numeric|min:0',
        ]);
        
        $qty = (int) $request->Qty;
        $harga = (float) $request->Harga;
        $total = $qty * $harga;
        
        DB::statement("
            UPDATE dbo.WebObapayEdit
            SET NamaItem = ?,
                Code = ?,
                Unit = ?,
                Qty = ?,
                Harga = ?,
                Total = ?,
                TotalEdit = ?,
                IsEdited = 1,
                UpdatedAt = GETDATE()
            WHERE IDObapay = ?
        ", [
            $request->NamaItem,
            $request->Code,
            $request->Unit,
            $qty,
            $harga,
            $total,
            $total,
            $request->IDObapay
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Qty dan subtotal ObaPay berhasil diperbarui.',
            'total' => $total
        ]);
    }
    public function delete(Request $request)
    {
        if (!in_array(strtolower(Auth::user()->Role), ['kasir', 'casemix', 'perawat'])) {
            abort(403, 'Anda tidak memiliki hak menghapus ObaPay.');
        }

        $request->validate([
            'IDObapay' => 'required|integer'
        ]);

        DB::statement("
            DELETE FROM dbo.WebObapayEdit
            WHERE IDObapay = ?
        ", [
            $request->IDObapay
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data ObaPay berhasil dihapus.'
        ]);
    }

    public function total($id)
    {
        $total = DB::table('dbo.WebObapayEdit')
            ->where('ID', $id)
            ->sum('TotalEdit');

        return response()->json([
            'success' => true,
            'total' => $total
        ]);
    }

    private function getFarmasiToken()
    {
        return Cache::remember('farmasi_token', 360, function () {

            $response = Http::post('http://192.168.1.9:8010/api/token', [
                'username' => env('FARMASI_USER'),
                'password' => env('FARMASI_PASS')
            ]);

            if (!$response->successful()) {
                throw new \Exception('Gagal mendapatkan token ObaPay');
            }

            return $response->json('token');
        });
    }

    public function searchMedicine(Request $request)
    {
        $token = $this->getFarmasiToken();

        $response = Http::withToken($token)
            ->timeout(15)
            ->get('http://192.168.1.9:8010/api/medicines', [
                'search' => $request->search
            ]);

        if ($response->status() == 401) {
            Cache::forget('farmasi_token');
            $token = $this->getFarmasiToken();

            $response = Http::withToken($token)
                ->timeout(15)
                ->get('http://192.168.1.9:8010/api/medicines', [
                    'search' => $request->search
                ]);
        }

        return response()->json($response->json());
    }

    public function store(Request $request)
    {
        if (!in_array(strtolower(Auth::user()->Role), ['kasir', 'casemix', 'perawat'])) {
            abort(403, 'Anda tidak memiliki hak menambah ObaPay.');
        }

        $request->validate([
            'ID'       => 'required|integer',
            'SaleID'   => 'required',
            'Tanggal'  => 'nullable',
            'NamaItem' => 'required',
            'Code'     => 'nullable|string|max:100',
            'Unit'     => 'nullable|string|max:50',
            'Qty'      => 'required|integer|min:1',
            'Harga'    => 'required|numeric|min:0',
        ]);

        $total = $request->Qty * $request->Harga;

        DB::table('dbo.WebObapayEdit')->insert([
            'ID'        => $request->ID,
            'SaleID'    => $request->SaleID,
            'Tanggal'   => $request->Tanggal ?? now(),
            'NamaItem'  => $request->NamaItem,
            'Code'      => $request->Code,
            'Unit'      => $request->Unit,
            'Qty'       => $request->Qty,
            'Harga'     => $request->Harga,
            'Total'     => $total,
            'TotalEdit' => $total,
            'IsEdited'  => 1,
            'CreatedAt' => DB::raw('GETDATE()'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Obat berhasil ditambahkan.'
        ]);
    }
}
