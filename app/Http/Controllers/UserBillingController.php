<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserBillingController extends Controller
{
    public function index()
    {
        $users = DB::table('UserBilling')
            ->orderBy('Nama')
            ->get();

        return view('userbilling', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'Nama' => 'required',
            'Username' => 'required',
            'Password' => 'required',
            'Role' => 'required',
        ]);

        DB::table('UserBilling')->insert([
            'Nama' => $request->Nama,
            'Username' => $request->Username,
            'Password' => Hash::make($request->Password),
            'Role' => $request->Role,
            'Aktif' => $request->Aktif ?? 1,
        ]);

        return redirect()
            ->route('userbilling.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'Nama' => 'required',
            'Username' => 'required',
            'Role' => 'required',
            'Aktif' => 'required',
        ]);

        $data = [
            'Nama' => $request->Nama,
            'Username' => $request->Username,
            'Role' => $request->Role,
            'Aktif' => $request->Aktif,
        ];

        // Password hanya diubah jika diisi
        if ($request->filled('Password')) {
            $data['Password'] = Hash::make($request->Password);
        }

        DB::table('UserBilling')
            ->where('ID', $id)
            ->update($data);

        return redirect()
            ->route('userbilling.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    public function destroy($id)
    {
        DB::table('UserBilling')
            ->where('ID', $id)
            ->delete();

        return redirect()
            ->route('userbilling.index')
            ->with('success', 'User berhasil dihapus.');
    }
    
}