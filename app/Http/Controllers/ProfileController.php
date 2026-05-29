<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function updatePassword(Request $request)
{
    $request->validate([
        'password_baru' => 'required|min:3|same:password_baru_confirmation',
    ], [
        'password_baru.required' => 'Password baru wajib diisi.',
        'password_baru.min' => 'Password baru minimal 3 karakter.',
        'password_baru.same' => 'Konfirmasi password baru tidak sama.',
    ]);

    DB::table('UserBilling')
        ->where('ID', $request->user_id)
        ->update([
            'Password' => Hash::make($request->password_baru),
        ]);

    return redirect('/rawat-inap')
        ->with('success', 'Password berhasil diganti.');
}
}