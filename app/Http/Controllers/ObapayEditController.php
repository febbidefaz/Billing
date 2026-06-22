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

            foreach ($sales as $sale) {
                $items = $sale['items'] ?? [];

                foreach ($items as $item) {
                    DB::statement("
                    IF EXISTS (
                        SELECT 1 FROM dbo.WebObapayEdit
                        WHERE ID = ?
                        AND SaleID = ?
                        AND NamaItem = ?
                    )
                    BEGIN
                        UPDATE dbo.WebObapayEdit
                        SET Tanggal = ?,
                            Qty = ?,
                            Harga = ?,
                            Total = ?,
                            TotalEdit = CASE 
                                WHEN ISNULL(IsEdited, 0) = 1 THEN TotalEdit
                                ELSE ?
                            END,
                            UpdatedAt = GETDATE()
                        WHERE ID = ?
                        AND SaleID = ?
                        AND NamaItem = ?
                    END
                    ELSE
                    BEGIN
                        INSERT INTO dbo.WebObapayEdit
                        (
                            ID, SaleID, Tanggal, NamaItem, Qty, Harga,
                            Total, TotalEdit, IsEdited, CreatedAt
                        )
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0, GETDATE())
                    END
                ", [
                    // IF EXISTS
                    $id,
                    $sale['transaction_no'] ?? null,
                    $item['name'] ?? null,
                
                    // UPDATE
                    $sale['date'] ?? null,
                    $item['qty'] ?? 0,
                    $item['unit_price'] ?? 0,
                    $item['subtotal'] ?? 0,
                    $item['subtotal'] ?? 0,
                    $id,
                    $sale['transaction_no'] ?? null,
                    $item['name'] ?? null,
                
                    // INSERT
                    $id,
                    $sale['transaction_no'] ?? null,
                    $sale['date'] ?? null,
                    $item['name'] ?? null,
                    $item['qty'] ?? 0,
                    $item['unit_price'] ?? 0,
                    $item['subtotal'] ?? 0,
                    $item['subtotal'] ?? 0,
                ]);
                }
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
            'Qty'      => 'required|integer|min:0',
            'Harga'    => 'required|numeric|min:0',
        ]);
        
        $qty = (int) $request->Qty;
        $harga = (float) $request->Harga;
        $total = $qty * $harga;
        
        DB::statement("
            UPDATE dbo.WebObapayEdit
            SET NamaItem = ?,
                Qty = ?,
                Harga = ?,
                Total = ?,
                TotalEdit = ?,
                IsEdited = 1,
                UpdatedAt = GETDATE()
            WHERE IDObapay = ?
        ", [
            $request->NamaItem,
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
            'Qty'      => 'required|integer|min:1',
            'Harga'    => 'required|numeric|min:0',
        ]);

        $total = $request->Qty * $request->Harga;

        DB::table('dbo.WebObapayEdit')->insert([
            'ID'        => $request->ID,
            'SaleID'    => $request->SaleID,
            'Tanggal'   => $request->Tanggal ?? now(),
            'NamaItem'  => $request->NamaItem,
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
