<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CasemixReadOnly
{
    public function handle(Request $request, Closure $next): Response
    {
        $allowedRoutes = [
            'rawatinap.updateDijaminPlafon',
            'rawatinap.hapusDijaminPlafon',
        ];
        
        if (
            auth()->check() &&
            auth()->user()->Role === 'casemix' &&
            !$request->isMethod('get')
        ) {
            abort(403, 'Role Casemix hanya dapat melihat data.');
        }

        return $next($request);
    }
}