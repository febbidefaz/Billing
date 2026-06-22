<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PerawatReadOnly
{
    public function handle(Request $request, Closure $next): Response
    {   
        $allowedRoutes = [
            'rawatinap.updateDijaminPlafon',
            'rawatinap.hapusDijaminPlafon',
        ];
        
        if (
            auth()->check() &&
            auth()->user()->Role === 'perawat' &&
            !$request->isMethod('get')
        ) {
            abort(403, 'Role Perawat hanya dapat melihat data.');
        }

        return $next($request);
    }
}