<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsPelanggan
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
         if (! $request->user() || $request->user()->role !== 'pelanggan') {
            abort(403, 'Halaman ini khusus untuk pelanggan.');
        }
        return $next($request);
    }
}
