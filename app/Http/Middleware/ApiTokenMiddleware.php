<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class ApiTokenMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();

        // Token tidak dikirim
        if (!$token) {
            return response()->json([
                'status' => false,
                'message' => 'Token tidak ditemukan. Silakan ambil token terlebih dahulu'
            ], 401);
        }

        try {
            $payload = json_decode(
                Crypt::decryptString($token),
                true
            );

            if (!$payload || !isset($payload['expired_at'])) {
                return response()->json([
                    'status' => false,
                    'message' => 'Token tidak valid.'
                ], 401);
            }

            // Token sudah lebih dari 10 menit
            if (time() > $payload['expired_at']) {
                return response()->json([
                    'status' => false,
                    'message' => 'Token sudah kedaluwarsa. Silakan ambil token baru melalui /api/get-token.'
                ], 401);
            }

            // Optional: data user bisa digunakan oleh controller
            $request->attributes->set('token_user', $payload);

            return $next($request);

        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => 'Token tidak valid atau tidak dapat dibaca.'
            ], 401);
        }
    }
}