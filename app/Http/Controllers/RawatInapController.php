<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class RawatInapController extends Controller
{
        public function index()
    {
        $data = DB::select("EXEC dbo.DaftarPasienRawatInap_SP");

        return view('rawatinap', compact('data'));
    }
}
