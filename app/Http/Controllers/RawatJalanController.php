<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RawatJalanController extends Controller
{
    public function index()
    {
        return view('rawatjalan');
    }

    public function data(Request $request)
    {
        $tgl1 = $request->tgl1 ?? date('Y-m-d');
        $tgl2 = $request->tgl2 ?? date('Y-m-d');
    
        $data = DB::select(
            "EXEC dbo.DaftarPasienRawatJalanKasirDate_SP ?, ?",
            [$tgl1, $tgl2]
        );
    
        return response()->json([
            'data' => $data
        ]);
    }
}

